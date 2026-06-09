@extends('admin.layouts.app')
@section('title', 'Sửa danh mục')
@section('page-title', 'Sửa danh mục')
@section('content')
    <div class="x_panel">
        <div class="x_title"><h2>Sửa danh mục</h2></div>
        <div class="x_content">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="admin-form-narrow">
                @csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Tên *</label><input name="name" value="{{ old('name', $category->name) }}" required class="form-control"></div>
                <div class="mb-3"><label class="form-label">Thứ tự</label><input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="form-control"></div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
@endsection
