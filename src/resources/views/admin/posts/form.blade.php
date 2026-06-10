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

            <div class="mb-3" x-data="featuredImage(@js($existingFeaturedImage))">
                <label class="form-label">Ảnh đại diện *</label>
                <input type="file" x-ref="fileInput" name="image" accept="image/*" @change="addFile($event)" class="form-control">
                @error('image')<p class="field-error">{{ $message }}</p>@enderror

                <template x-if="existing">
                    <input type="hidden" name="existing_image" :value="existing.path">
                </template>

                <div class="image-grid" x-show="existing || newImage" style="display:none">
                    <template x-if="existing">
                        <div class="image-card">
                            <img :src="existing.url" alt="">
                            <div class="image-card-bar">
                                <button type="button" class="image-card-remove" @click="removeExisting()">Xóa</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="newImage">
                        <div class="image-card">
                            <img :src="newImage.url" alt="">
                            <div class="image-card-bar">
                                <button type="button" class="image-card-remove" @click="removeNew()">Xóa</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mb-3"><label class="form-label">Tóm tắt</label><textarea name="excerpt" rows="10" class="form-control">{{ old('excerpt', $post->excerpt ?? '') }}</textarea></div>
            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                @include('admin.partials.rich-editor', ['name' => 'content', 'value' => $post->content ?? ''])
            </div>
            <div class="form-check mb-4">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" id="is_published" class="form-check-input" @checked((string) old('is_published', ($post->is_published ?? true) ? '1' : '0') === '1')>
                <label for="is_published" class="form-check-label">Xuất bản</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
