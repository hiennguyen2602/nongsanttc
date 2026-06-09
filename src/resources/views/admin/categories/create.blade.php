@extends('admin.layouts.app')
@section('title', 'Thêm danh mục')
@section('page-title', 'Thêm danh mục')
@section('content')
    <div class="x_panel">
        <div class="x_title"><h2>Thêm danh mục</h2></div>
        <div class="x_content">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="max-w-xl">
                @csrf
                <div class="mb-3"><label class="form-label">Tên *</label><input name="name" required class="form-control"></div>
                <div class="mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control"></div>
                <div class="mb-4"><label class="form-label">Thứ tự</label><input type="number" name="sort_order" value="0" class="form-control"></div>
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
@endsection
