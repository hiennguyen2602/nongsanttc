<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Services\RecentlyViewedService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->where('is_active', true)->with('category');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('name', 'like', "%{$q}%");
        }

        return view('store.products.index', [
            'products' => $query->latest()->paginate(12)->withQueryString(),
            'categories' => Category::orderBy('sort_order')->get(),
            'activeCategory' => $request->category,
        ]);
    }

    public function show(string $slug, RecentlyViewedService $recentlyViewed): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'variants'])
            ->firstOrFail();

        $recentlyViewed->track($product->id);

        return view('store.products.show', [
            'product' => $product,
            'relatedProducts' => Product::query()
                ->where('is_active', true)
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
                ->limit(4)
                ->get(),
            'viewedProducts' => $recentlyViewed->products($product->id),
            'promotions' => Promotion::query()->where('is_active', true)->orderBy('min_order')->get(),
        ]);
    }
}
