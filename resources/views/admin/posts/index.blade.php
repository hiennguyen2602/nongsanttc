@extends('admin.layouts.app')
@section('title', 'Bài viết')
@section('page-title', 'Bài viết')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Bài viết'],
    ])])
@endsection
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Bài viết</h2>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">+ Thêm bài viết</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th class="cell-image">Ảnh</th><th class="cell-text-wide">Tiêu đề</th><th class="cell-date">Ngày</th><th class="cell-status">Trạng thái</th><th class="table-actions"></th></tr></thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td class="cell-image">
                                    @if ($post->image)
                                        <img src="{{ store_media_url($post->image, 'thumbnail') }}" alt="" class="h-12 w-20 max-w-none shrink-0 rounded object-cover ring-1 ring-slate-200">
                                    @endif
                                </td>
                                <td class="cell-text-wide font-medium"><a href="{{ route('admin.posts.show', $post) }}" class="admin-link hover:underline">{{ $post->title }}</a></td>
                                <td class="cell-date">{{ $post->published_at?->format('d/m/Y') }}</td>
                                <td class="cell-status">@include('admin.partials.status-badge', ['label' => $post->publishStatusLabel(), 'class' => $post->publishStatusBadgeClass()])</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-link btn-sm">Sửa</a>
                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" data-confirm-entity="bài viết" data-confirm-name="{{ $post->title }}">@csrf @method('DELETE')<button type="button" class="btn btn-link btn-link-danger btn-sm" data-confirm-trigger>Xóa</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $posts->links() }}</div>
        </div>
    </div>
@endsection
