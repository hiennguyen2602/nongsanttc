@extends('admin.layouts.app')
@section('title', 'Sửa sản phẩm')
@section('page-title', 'Sửa sản phẩm')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Danh sách sản phẩm', 'url' => route('admin.products.index')],
        ['label' => 'Thông tin sản phẩm', 'url' => route('admin.products.show', $product)],
        ['label' => 'Sửa sản phẩm'],
    ])])
@endsection
@section('content')
    @include('admin.products._form', ['action' => route('admin.products.update', $product), 'method' => 'PUT', 'product' => $product])
@endsection
