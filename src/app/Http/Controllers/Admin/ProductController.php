<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
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

    public function store(StoreProductRequest $request, ImageUploadService $uploader): RedirectResponse
    {
        $data = $request->toModelData();

        [$image, $gallery] = $this->handleImages($request, $uploader, null);
        $data['image'] = $image;
        $data['gallery'] = $gallery;

        $product = Product::query()->create($data);
        $this->syncVariants($product, $request);

        return redirect()->route('admin.products.show', $product)->with('success', 'Thêm sản phẩm thành công.');
    }

    public function edit(Product $product): View
    {
        $product->load('variants');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function update(
        UpdateProductRequest $request,
        Product $product,
        ImageUploadService $uploader,
        EditorImageService $editorImages,
    ): RedirectResponse {
        $data = $request->toModelData();
        $oldDescription = $product->description;

        [$image, $gallery] = $this->handleImages($request, $uploader, $product);
        $data['image'] = $image;
        $data['gallery'] = $gallery;

        $product->update($data);
        $this->syncVariants($product, $request);
        $editorImages->deleteRemoved($oldDescription, $data['description'] ?? null, $uploader);

        return redirect()->route('admin.products.show', $product)->with('success', 'Cập nhật sản phẩm thành công.');
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

    /** @return list<string> */
    private function keptProductImages(StoreProductRequest|UpdateProductRequest $request, ?Product $product): array
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
    private function handleImages(
        StoreProductRequest|UpdateProductRequest $request,
        ImageUploadService $uploader,
        ?Product $product,
    ): array {
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
    private function syncVariants(Product $product, StoreProductRequest|UpdateProductRequest $request): void
    {
        $variants = $request->input('variants', []);
        $keptIds = [];
        $existingVariants = $product->variants()->get()->keyBy('id');

        foreach ($variants as $variant) {
            if (empty($variant['price'])) {
                continue;
            }

            $payload = [
                'flavor' => $variant['flavor'] ?? null,
                'size' => $variant['size'] ?? null,
                'price' => (int) $variant['price'],
                'stock' => filled($variant['stock'] ?? null) ? (int) $variant['stock'] : null,
            ];

            $id = $variant['id'] ?? null;
            $existing = $id ? $existingVariants->get((int) $id) : null;

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
