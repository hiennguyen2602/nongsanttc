@php
    $existingDesktop = ! empty($banner?->image)
        ? ['path' => $banner->image, 'url' => store_media_url($banner->image)]
        : null;
    $existingMobile = ! empty($banner?->image_mobile)
        ? ['path' => $banner->image_mobile, 'url' => store_media_url($banner->image_mobile)]
        : null;
@endphp

<div class="x_panel">
    <div class="x_title"><h2>{{ isset($banner) ? 'Sửa banner' : 'Thêm banner' }}</h2></div>
    <div class="x_content">
        <form method="POST" action="{{ isset($banner) ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" enctype="multipart/form-data" class="admin-form-narrow">
            @csrf @if(isset($banner)) @method('PUT') @endif
            <div class="mb-3"><label class="form-label">Tiêu đề *</label><input name="title" value="{{ old('title', $banner->title ?? '') }}" required class="form-control">@error('title')<p class="field-error">{{ $message }}</p>@enderror</div>
            <div class="mb-3"><label class="form-label">Phụ đề</label><input name="subtitle" value="{{ old('subtitle', $banner->subtitle ?? '') }}" class="form-control"></div>
            <div class="mb-3"><label class="form-label">Link</label><input name="link" value="{{ old('link', $banner->link ?? '') }}" class="form-control" placeholder="/san-pham hoặc https://..."></div>
            <div class="mb-3">
                <label class="form-label">Vị trí *</label>
                <select name="position" class="form-select" required>
                    @foreach(\App\Models\Banner::positionLabels() as $val => $label)
                        <option value="{{ $val }}" @selected(old('position', $banner->position ?? 'home_cta') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">Banner hiển thị ở khối quảng cáo giữa trang chủ (dưới sản phẩm nổi bật).</p>
                @error('position')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="mb-3"><label class="form-label">Thứ tự</label><input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0" class="form-control"></div>

            <div class="mb-3" x-data="featuredImage(@js($existingDesktop))">
                <label class="form-label">Ảnh desktop {{ isset($banner) ? '' : '*' }}</label>
                <input type="file" x-ref="fileInput" name="image" accept="image/*" @change="addFile($event)" {{ isset($banner) ? '' : 'required' }} class="form-control">
                @error('image')<p class="field-error">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-500">Giữ nguyên tỷ lệ khi lưu. Trên web hiển thị khung ngang 16:10 — nên dùng ảnh ngang (vd. 1200×750) để đẹp nhất.</p>
                <template x-if="existing">
                    <input type="hidden" name="existing_image" :value="existing.path">
                </template>
                <div class="image-grid mt-2" x-show="existing || newImage" style="display:none">
                    <template x-if="existing">
                        <div class="image-card">
                            <img :src="existing.url" alt="" class="aspect-[3/1] w-full object-cover">
                            <div class="image-card-bar">
                                <button type="button" class="image-card-remove" @click="removeExisting()">Xóa</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="newImage">
                        <div class="image-card">
                            <img :src="newImage.url" alt="" class="aspect-[3/1] w-full object-cover">
                            <div class="image-card-bar">
                                <button type="button" class="image-card-remove" @click="removeNew()">Xóa</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mb-3" x-data="featuredImage(@js($existingMobile))">
                <label class="form-label">Ảnh mobile</label>
                <input type="file" x-ref="fileInput" name="image_mobile" accept="image/*" @change="addFile($event)" class="form-control">
                @error('image_mobile')<p class="field-error">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-500">Giữ nguyên tỷ lệ ảnh — chỉ thu nhỏ nếu rộng hơn 768px. Nếu bỏ trống sẽ dùng ảnh desktop.</p>
                <template x-if="existing">
                    <input type="hidden" name="existing_image_mobile" :value="existing.path">
                </template>
                <div class="image-grid mt-2" x-show="existing || newImage" style="display:none">
                    <template x-if="existing">
                        <div class="image-card">
                            <img :src="existing.url" alt="" class="aspect-[768/500] w-full object-cover">
                            <div class="image-card-bar">
                                <button type="button" class="image-card-remove" @click="removeExisting()">Xóa</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="newImage">
                        <div class="image-card">
                            <img :src="newImage.url" alt="" class="aspect-[768/500] w-full object-cover">
                            <div class="image-card-bar">
                                <button type="button" class="image-card-remove" @click="removeNew()">Xóa</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="form-check mb-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" @checked((string) old('is_active', ($banner->is_active ?? true) ? '1' : '0') === '1')>
                <label for="is_active" class="form-check-label">{{ \App\Models\Banner::visibilityLabels()['active'] }}</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
