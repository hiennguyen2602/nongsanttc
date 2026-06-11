@extends('admin.layouts.app')
@section('title', 'Danh mục')
@section('page-title', 'Danh mục')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Danh mục'],
    ])])
@endsection
@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>Danh mục</h2>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">+ Thêm danh mục</a>
        </div>
        <div class="x_content">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th class="cell-text">Tên</th><th class="cell-num">Thứ tự</th><th class="table-actions"></th></tr></thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="cell-text">{{ $category->name }}</td>
                                <td class="cell-num">{{ $category->sort_order }}</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-link btn-sm">Sửa</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Xóa?')">@csrf @method('DELETE')<button type="submit" class="btn btn-link btn-link-danger btn-sm">Xóa</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $categories->links() }}</div>
        </div>
    </div>
@endsection
