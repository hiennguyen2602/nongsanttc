<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Post;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('store.home', [
            'featuredProducts' => Product::query()
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->limit(8)
                ->get(),
            'posts' => Post::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
            'banners' => Banner::query()
                ->where('is_active', true)
                ->where('position', 'home_cta')
                ->where('image', '!=', '')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
