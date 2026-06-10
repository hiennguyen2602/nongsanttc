@extends('admin.layouts.app')
@section('title', 'Cài đặt')
@section('page-title', 'Cài đặt website')
@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @foreach ($groups as $group => $items)
            <div class="x_panel">
                <div class="x_title"><h2>{{ $groupLabels[$group] ?? ucfirst($group) }}</h2></div>
                <div class="x_content">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($items as $item)
                            @php
                                $colSpan = $fieldColSpan[$item->key] ?? ($item->type === 'textarea' ? 2 : 1);
                            @endphp
                            <div class="mb-3 {{ $colSpan === 2 ? 'md:col-span-2' : '' }}">
                                @if ($item->type === 'image')
                                    @php
                                        $existingSettingImage = $item->value
                                            ? ['path' => $item->value, 'url' => store_media_url($item->value, 'medium')]
                                            : null;
                                        $settingImageHint = match ($item->key) {
                                            'hero_desktop' => 'Giữ nguyên tỷ lệ ảnh — chỉ thu nhỏ nếu rộng hơn ' . config('media.hero_desktop_max_width', 1920) . 'px. Trên web hiển thị full màn hình (object-cover).',
                                            'hero_mobile' => 'Giữ nguyên tỷ lệ ảnh — chỉ thu nhỏ nếu rộng hơn ' . config('media.hero_mobile_max_width', 768) . 'px. Dùng cho màn hình điện thoại.',
                                            default => null,
                                        };
                                    @endphp
                                    @include('admin.partials.image-upload', [
                                        'name' => $item->key,
                                        'label' => $item->label ?? $item->key,
                                        'existing' => $existingSettingImage,
                                        'existingField' => 'existing_' . $item->key,
                                        'hint' => $settingImageHint,
                                    ])
                                @else
                                    <label class="form-label">{{ $item->label ?? $item->key }}</label>

                                    @if ($item->type === 'textarea')
                                        <textarea name="{{ $item->key }}" rows="4" class="form-control">{{ old($item->key, $item->value) }}</textarea>
                                    @else
                                        <input type="text" name="{{ $item->key }}" value="{{ old($item->key, $item->value) }}" class="form-control">
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
    </form>
@endsection
