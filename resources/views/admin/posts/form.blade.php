@php
    $existingFeaturedImage = ! empty($post?->image)
        ? ['path' => $post->image, 'url' => store_media_url($post->image, 'medium')]
        : null;
@endphp

<div class="x_panel">
    <div class="x_title"><h2>{{ isset($post) ? 'Sửa bài viết' : 'Thêm bài viết' }}</h2></div>
    <div class="x_content">
        <form method="POST" action="{{ isset($post) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data" class="admin-form-post">
            @csrf @if(isset($post)) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Tiêu đề *</label><input name="title" value="{{ old('title', $post->title ?? '') }}" required class="form-control"></div>

            @include('admin.partials.image-upload', [
                'name' => 'image',
                'label' => 'Ảnh đại diện',
                'existing' => $existingFeaturedImage,
                'existingField' => 'existing_image',
                'required' => ! isset($post),
            ])

            <div class="mb-3"><label class="form-label">Tóm tắt</label><textarea name="excerpt" rows="10" class="form-control">{{ old('excerpt', $post->excerpt ?? '') }}</textarea></div>
            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                @include('admin.partials.rich-editor', ['name' => 'content', 'value' => $post->content ?? ''])
            </div>
            <div class="mb-3">
                <label class="form-label">Tiêu đề SEO</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}" maxlength="255" class="form-control" placeholder="Để trống = dùng tiêu đề bài viết">
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả SEO</label>
                <textarea name="meta_description" rows="3" maxlength="320" class="form-control" placeholder="Để trống = tự lấy từ tóm tắt/nội dung">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">Tối đa ~160 ký tự hiển thị trên Google.</p>
            </div>
            <div class="form-check mb-4">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" id="is_published" class="form-check-input" @checked((string) old('is_published', ($post->is_published ?? true) ? '1' : '0') === '1')>
                <label for="is_published" class="form-check-label">{{ \App\Models\Post::publishStatusLabels()['published'] }}</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
