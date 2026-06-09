<div class="x_panel">
    <div class="x_title"><h2>{{ isset($banner) ? 'Sửa banner' : 'Thêm banner' }}</h2></div>
    <div class="x_content">
        <form method="POST" action="{{ isset($banner) ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" enctype="multipart/form-data" class="max-w-2xl">
            @csrf @if(isset($banner)) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Tiêu đề *</label><input name="title" value="{{ old('title', $banner->title ?? '') }}" required class="form-control"></div>
            <div class="mb-3"><label class="form-label">Phụ đề</label><input name="subtitle" value="{{ old('subtitle', $banner->subtitle ?? '') }}" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Link</label><input name="link" value="{{ old('link', $banner->link ?? '') }}" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Vị trí</label><input name="position" value="{{ old('position', $banner->position ?? 'home_cta') }}" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Thứ tự</label><input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="form-control"></div>
            <div class="mb-3">
                <label class="form-label">Ảnh desktop {{ isset($banner) ? '' : '*' }}</label>
                @if(!empty($banner?->image))<img src="{{ store_media_url($banner->image, 'medium') }}" class="mb-2 h-24 rounded object-cover ring-1 ring-slate-200">@endif
                <input type="file" name="image" accept="image/*" {{ isset($banner) ? '' : 'required' }} class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Ảnh mobile</label>
                @if(!empty($banner?->image_mobile))<img src="{{ store_media_url($banner->image_mobile, 'medium') }}" class="mb-2 h-24 rounded object-cover ring-1 ring-slate-200">@endif
                <input type="file" name="image_mobile" accept="image/*" class="form-control">
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" @checked(old('is_active', $banner->is_active ?? true))>
                <label for="is_active" class="form-check-label">Hiển thị</label>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
