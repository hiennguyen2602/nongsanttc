<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\EditorImageService;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->q, fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->latest('updated_at')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'variants']);

        return view('admin.products.show', compact('product'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('sort_order')->get(),
            'suggestedSku' => generate_unique_sku(),
        ]);
    }

    public function store(Request $request, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request);
        $this->ensureHasImage($request, null);
        $this->validateVariants($request);

        [$image, $gallery] = $this->handleImages($request, $uploader, null);
        $data['image'] = $image;
        $data['gallery'] = $gallery;

        $product = Product::query()->create($data);
        $this->syncVariants($product, $request);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công.');
    }

    public function edit(Product $product): View
    {
        $product->load('variants');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Product $product, ImageUploadService $uploader, EditorImageService $editorImages): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $this->ensureHasImage($request, $product);
        $this->validateVariants($request);

        $oldDescription = $product->description;

        [$image, $gallery] = $this->handleImages($request, $uploader, $product);
        $data['image'] = $image;
        $data['gallery'] = $gallery;

        $product->update($data);
        $this->syncVariants($product, $request);
        $editorImages->deleteRemoved($oldDescription, $data['description'] ?? null, $uploader);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product, ImageUploadService $uploader, EditorImageService $editorImages): RedirectResponse
    {
        $editorImages->deletePaths($editorImages->extractPaths($product->description), $uploader);

        foreach (array_filter(array_merge([$product->image], (array) $product->gallery)) as $path) {
            $uploader->delete($path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'sale_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => image_upload_file_rules(['nullable']),
        ], image_upload_validation_messages('images.*'));

        unset($data['images']);

        $data['slug'] = generate_unique_slug($data['name'], 'products', $product?->id);

        $sku = trim((string) ($data['sku'] ?? ''));
        $data['sku'] = $sku !== '' ? $sku : generate_unique_sku();

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    /**
     * Biến thể có Vị hoặc Size thì bắt buộc phải có giá.
     */
    private function validateVariants(Request $request): void
    {
        $errors = [];

        foreach ((array) $request->input('variants', []) as $index => $variant) {
            $flavor = trim((string) ($variant['flavor'] ?? ''));
            $size = trim((string) ($variant['size'] ?? ''));
            $price = trim((string) ($variant['price'] ?? ''));

            if (($flavor !== '' || $size !== '') && $price === '') {
                $errors["variants.{$index}.price"] = 'Vui lòng nhập giá cho biến thể.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Yêu cầu sản phẩm phải có ít nhất một ảnh (ảnh cũ giữ lại hoặc ảnh mới).
     */
    private function ensureHasImage(Request $request, ?Product $product): void
    {
        $hasNew = collect((array) $request->file('images', []))->filter()->isNotEmpty();
        $hasKept = count($this->keptProductImages($request, $product)) > 0;

        if (! $hasNew && ! $hasKept) {
            throw ValidationException::withMessages([
                'images' => 'Vui lòng chọn ít nhất một ảnh sản phẩm.',
            ]);
        }
    }

    /** @return list<string> */
    private function keptProductImages(Request $request, ?Product $product): array
    {
        $submitted = (array) $request->input('existing_images', []);

        if ($product === null) {
            foreach ($submitted as $path) {
                if (filled($path)) {
                    throw ValidationException::withMessages([
                        'images' => 'Ảnh không hợp lệ.',
                    ]);
                }
            }

            return [];
        }

        $allowed = array_filter(array_merge([$product->image], (array) $product->gallery));

        return kept_upload_paths($submitted, $allowed, 'uploads/products', 'images');
    }

    /**
     * Xử lý ảnh: giữ ảnh cũ, upload ảnh mới, chọn ảnh chính.
     *
     * @return array{0: ?string, 1: array<int, string>}
     */
    private function handleImages(Request $request, ImageUploadService $uploader, ?Product $product): array
    {
        $kept = $this->keptProductImages($request, $product);

        if ($product) {
            $previous = array_filter(array_merge([$product->image], (array) $product->gallery));
            foreach ($previous as $path) {
                if (! in_array($path, $kept, true)) {
                    $uploader->delete($path);
                }
            }
        }

        $uploaded = [];
        foreach ((array) $request->file('images', []) as $file) {
            if ($file) {
                $uploaded[] = $uploader->upload($file, 'uploads/products/' . date('Y/m'))['path'];
            }
        }

        $all = array_values(array_merge($kept, $uploaded));

        $selector = (string) $request->input('main_selector', '');
        $main = null;

        if (str_starts_with($selector, 'existing:')) {
            $candidate = substr($selector, 9);
            $main = in_array($candidate, $all, true) ? $candidate : null;
        } elseif (str_starts_with($selector, 'new:')) {
            $index = (int) substr($selector, 4);
            $main = $uploaded[$index] ?? null;
        }

        if (! $main) {
            $main = $all[0] ?? null;
        }

        $gallery = array_values(array_filter($all, fn ($path) => $path !== $main));

        return [$main, $gallery];
    }

    /**
     * Đồng bộ biến thể: giữ nguyên ID của biến thể cũ (update), tạo mới biến thể chưa có,
     * và chỉ xóa những biến thể đã bị bỏ. Tránh đổi ID làm hỏng giỏ hàng/đơn cũ.
     */
    private function syncVariants(Product $product, Request $request): void
    {
        $variants = $request->input('variants', []);
        $keptIds = [];

        foreach ($variants as $variant) {
            if (empty($variant['price'])) {
                continue;
            }

            $payload = [
                'flavor' => $variant['flavor'] ?? null,
                'size' => $variant['size'] ?? null,
                'price' => (int) $variant['price'],
                'stock' => (int) ($variant['stock'] ?? 0),
            ];

            $id = $variant['id'] ?? null;
            $existing = $id ? $product->variants()->whereKey($id)->first() : null;

            if ($existing) {
                $existing->update($payload);
                $keptIds[] = $existing->id;
            } else {
                $keptIds[] = $product->variants()->create($payload)->id;
            }
        }

        $product->variants()->whereNotIn('id', $keptIds)->delete();
    }
}
