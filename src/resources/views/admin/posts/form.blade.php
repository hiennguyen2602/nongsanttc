<div class="x_panel">
    <div class="x_title"><h2>{{ isset($post) ? 'Sửa bài viết' : 'Thêm bài viết' }}</h2></div>
    <div class="x_content">
        <form method="POST" action="{{ isset($post) ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data" class="max-w-3xl">
            @csrf @if(isset($post)) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Tiêu đề *</label><input name="title" value="{{ old('title', $post->title ?? '') }}" required class="form-control"></div>
            <div class="mb-3"><label class="form-label">Slug</label><input name="slug" value="{{ old('slug', $post->slug ?? '') }}" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Tóm tắt</label><textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $post->excerpt ?? '') }}</textarea></div>
            <div class="mb-3"><label class="form-label">Nội dung</label>@include('admin.partials.rich-editor', ['name' => 'content', 'value' => $post->content ?? ''])</div>
            <div class="mb-3">
                <label class="form-label">Ảnh đại diện</label>
                @if(!empty($post?->image))<img src="{{ store_media_url($post->image, 'medium') }}" class="mb-2 h-24 rounded object-cover ring-1 ring-slate-200">@endif
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" name="is_published" value="1" id="is_published" class="form-check-input" @checked(old('is_published', $post->is_published ?? true))>
                <label for="is_published" class="form-check-label">Xuất bản</label>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
