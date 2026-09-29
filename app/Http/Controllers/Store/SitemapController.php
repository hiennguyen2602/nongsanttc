<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $staticPages = [
            ['loc' => route('home', absolute: true), 'lastmod' => now()],
            ['loc' => route('products.index', absolute: true), 'lastmod' => Product::query()->where('is_active', true)->max('updated_at')],
            ['loc' => route('posts.index', absolute: true), 'lastmod' => Post::query()->where('is_published', true)->max('updated_at')],
            ['loc' => route('about', absolute: true), 'lastmod' => null],
            ['loc' => route('contact', absolute: true), 'lastmod' => null],
        ];

        $products = Product::query()
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        $posts = Post::query()
            ->where('is_published', true)
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        return response()
            ->view('store.sitemap', compact('staticPages', 'products', 'posts'))
            ->header('Content-Type', 'application/xml');
    }
}
