<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.create');
    }

    public function store(Request $request, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $uploader->upload($request->file('image'), 'uploads/posts/' . date('Y/m'))['path'];
        }

        Post::query()->create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Thêm bài viết thành công.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post, ImageUploadService $uploader): RedirectResponse
    {
        $data = $this->validated($request, $post);

        if ($request->hasFile('image')) {
            $uploader->delete($post->image);
            $data['image'] = $uploader->upload($request->file('image'), 'uploads/posts/' . date('Y/m'))['path'];
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công.');
    }

    public function destroy(Post $post, ImageUploadService $uploader): RedirectResponse
    {
        $uploader->delete($post->image);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công.');
    }

    private function validated(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published', true);
        $data['published_at'] = $data['published_at'] ?? now();

        return $data;
    }
}
