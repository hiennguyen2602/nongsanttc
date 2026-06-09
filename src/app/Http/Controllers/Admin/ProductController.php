<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
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
            ->latest()
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
        $this->ensureHasImage($request);

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

    public function update(Request $request, Product $product, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $this->ensureHasImage($request);

        [$image, $gallery] = $this->handleImages($request, $uploader, $product);
        $data['image'] = $image;
        $data['gallery'] = $gallery;

        $product->update($data);
        $product->variants()->delete();
        $this->syncVariants($product, $request);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product, ImageUploadService $uploader): RedirectResponse
    {
        foreach (array_filter(array_merge([$product->image], (array) $product->gallery)) as $path) {
            $uploader->delete($path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $maxMb = (float) config('media.max_image_mb', 5);
        $maxKb = (int) round($maxMb * 1024);
        $maxLabel = rtrim(rtrim(number_format($maxMb, 1), '0'), '.');

        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:0'],
            'sale_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:' . $maxKb],
        ], [
            'images.*.image' => 'File tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Ảnh phải có định dạng: jpeg, jpg, png, webp, gif.',
            'images.*.max' => 'Mỗi ảnh không được vượt quá ' . $maxLabel . 'MB.',
            'images.*.uploaded' => 'Ảnh tải lên thất bại hoặc vượt quá dung lượng cho phép (' . $maxLabel . 'MB).',
        ]);

        unset($data['images']);

        $data['slug'] = generate_unique_slug($data['name'], 'products', $product?->id);

        $sku = trim((string) ($data['sku'] ?? ''));
        $data['sku'] = $sku !== '' ? $sku : generate_unique_sku();

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    /**
     * Yêu cầu sản phẩm phải có ít nhất một ảnh (ảnh cũ giữ lại hoặc ảnh mới).
     */
    private function ensureHasImage(Request $request): void
    {
        $hasNew = collect((array) $request->file('images', []))->filter()->isNotEmpty();
        $hasKept = collect((array) $request->input('existing_images', []))
            ->filter(fn ($path) => filled($path))
            ->isNotEmpty();

        if (! $hasNew && ! $hasKept) {
            throw ValidationException::withMessages([
                'images' => 'Vui lòng chọn ít nhất một ảnh sản phẩm.',
            ]);
        }
    }

    /**
     * Xử lý ảnh: giữ ảnh cũ, upload ảnh mới, chọn ảnh chính.
     *
     * @return array{0: ?string, 1: array<int, string>}
     */
    private function handleImages(Request $request, ImageUploadService $uploader, ?Product $product): array
    {
        $kept = array_values(array_filter((array) $request->input('existing_images', [])));

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

    private function syncVariants(Product $product, Request $request): void
    {
        $variants = $request->input('variants', []);

        foreach ($variants as $variant) {
            if (empty($variant['price'])) {
                continue;
            }

            ProductVariant::query()->create([
                'product_id' => $product->id,
                'flavor' => $variant['flavor'] ?? null,
                'size' => $variant['size'] ?? null,
                'price' => (int) $variant['price'],
                'sku' => $variant['sku'] ?? null,
                'stock' => (int) ($variant['stock'] ?? 0),
            ]);
        }
    }
}
