@extends('admin.layouts.app')
@section('title', 'Bài viết')
@section('page-title', 'Bài viết')
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Bài viết</h2>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">+ Thêm bài viết</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped posts-table">
                    <thead><tr><th>Tiêu đề</th><th>Ngày</th><th>Trạng thái</th><th class="table-actions"></th></tr></thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td class="font-medium"><a href="{{ route('admin.posts.show', $post) }}" class="admin-link hover:underline">{{ $post->title }}</a></td>
                                <td>{{ $post->published_at?->format('d/m/Y') }}</td>
                                <td><span class="badge {{ $post->is_published ? 'badge-success' : 'badge-secondary' }}">{{ $post->is_published ? 'Đã xuất bản' : 'Nháp' }}</span></td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-link btn-sm">Sửa</a>
                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')<button type="submit" class="btn btn-link btn-link-danger btn-sm">Xóa</button></form>
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
