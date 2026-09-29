<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use App\Services\EditorImageService;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::latest('updated_at')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.create');
    }

    public function store(PostRequest $request, ImageUploadService $uploader): RedirectResponse
    {
        $data = $request->toModelData();
        $data['image'] = $this->handleFeaturedImage($request, $uploader, null);

        $post = Post::query()->create($data);

        return redirect()->route('admin.posts.show', $post)->with('success', 'Thêm bài viết thành công.');
    }

    public function show(Post $post): View
    {
        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(
        PostRequest $request,
        Post $post,
        ImageUploadService $uploader,
        EditorImageService $editorImages,
    ): RedirectResponse {
        $data = $request->toModelData();
        $oldContent = $post->content;
        $data['image'] = $this->handleFeaturedImage($request, $uploader, $post);

        $post->update($data);
        $editorImages->deleteRemoved($oldContent, $data['content'] ?? null, $uploader);

        return redirect()->route('admin.posts.show', $post)->with('success', 'Cập nhật bài viết thành công.');
    }

    public function destroy(Post $post, ImageUploadService $uploader, EditorImageService $editorImages): RedirectResponse
    {
        $editorImages->deletePaths($editorImages->extractPaths($post->content), $uploader);
        $uploader->delete($post->image);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công.');
    }

    private function handleFeaturedImage(PostRequest $request, ImageUploadService $uploader, ?Post $post): string
    {
        if ($request->hasFile('image')) {
            if ($post?->image) {
                $uploader->delete($post->image);
            }

            return $uploader->upload(
                $request->file('image'),
                'uploads/posts/' . date('Y/m'),
                null,
                (int) config('media.post_featured_max_width', 600),
            )['path'];
        }

        if ($post === null) {
            throw ValidationException::withMessages([
                'image' => 'Vui lòng chọn ảnh đại diện.',
            ]);
        }

        $kept = resolve_kept_upload_path(
            $request->input('existing_image'),
            $post->image,
            'uploads/posts',
            'image',
        );

        if ($kept === null && filled($post->image)) {
            $uploader->delete($post->image);
        }

        return $kept ?? '';
    }
}
