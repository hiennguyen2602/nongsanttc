@extends('admin.layouts.app')
@section('title', 'Banner')
@section('page-title', 'Banner')
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Banner</h2>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-sm">+ Thêm banner</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Vị trí</th>
                            <th>Trạng thái</th>
                            <th class="table-actions"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($banners as $banner)
                            <tr>
                                <td><img src="{{ store_media_url($banner->image) }}" alt="" class="h-12 w-20 rounded object-cover ring-1 ring-slate-200"></td>
                                <td>{{ $banner->title }}</td>
                                <td>{{ $banner->positionLabel() }}</td>
                                <td>@include('admin.partials.status-badge', ['label' => $banner->visibilityLabel(), 'class' => $banner->visibilityBadgeClass()])</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-link btn-sm">Sửa</a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="inline" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')<button type="submit" class="btn btn-link btn-link-danger btn-sm">Xóa</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $banners->links() }}</div>
        </div>
    </div>
@endsection
