@extends('admin.layouts.app')
@section('title', 'Cài đặt')
@section('page-title', 'Cài đặt website')
@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @foreach ($groups as $group => $items)
            <div class="x_panel">
                <div class="x_title"><h2>{{ ucfirst($group) }}</h2></div>
                <div class="x_content">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @foreach ($items as $item)
                            <div class="mb-3 {{ in_array($item->type, ['textarea', 'image']) ? 'md:col-span-2' : '' }}">
                                <label class="form-label">{{ $item->label ?? $item->key }}</label>

                                @if ($item->type === 'image')
                                    @if ($item->value)
                                        <img src="{{ store_media_url($item->value, 'medium') }}" alt="" class="mb-2 h-24 rounded object-cover ring-1 ring-slate-200">
                                    @endif
                                    <input type="file" name="{{ $item->key }}" accept="image/*" class="form-control">
                                @elseif ($item->type === 'textarea')
                                    <textarea name="{{ $item->key }}" rows="4" class="form-control">{{ old($item->key, $item->value) }}</textarea>
                                @else
                                    <input type="text" name="{{ $item->key }}" value="{{ old($item->key, $item->value) }}" class="form-control">
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
