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
                                    @endphp
                                    @include('admin.partials.image-upload', [
                                        'name' => $item->key,
                                        'label' => $item->label ?? $item->key,
                                        'existing' => $existingSettingImage,
                                        'existingField' => 'existing_' . $item->key,
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
