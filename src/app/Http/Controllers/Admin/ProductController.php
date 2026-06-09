<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $uploader->upload($request->file('image'), 'uploads/products/' . date('Y/m'))['path'];
        }

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

        if ($request->hasFile('image')) {
            $uploader->delete($product->image);
            $data['image'] = $uploader->upload($request->file('image'), 'uploads/products/' . date('Y/m'))['path'];
        }

        $product->update($data);
        $product->variants()->delete();
        $this->syncVariants($product, $request);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product, ImageUploadService $uploader): RedirectResponse
    {
        $uploader->delete($product->image);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0'],
            'sale_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
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
