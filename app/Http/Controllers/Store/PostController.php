<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('store.posts.index', [
            'posts' => Post::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('store.posts.show', [
            'post' => $post,
            'recentPosts' => Post::query()
                ->where('is_published', true)
                ->where('id', '!=', $post->id)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
