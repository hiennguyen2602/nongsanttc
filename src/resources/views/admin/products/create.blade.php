@extends('admin.layouts.app')
@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Danh sách sản phẩm', 'url' => route('admin.products.index')],
        ['label' => 'Thêm sản phẩm'],
    ])])
@endsection
@section('content')
    @include('admin.products._form', ['action' => route('admin.products.store'), 'method' => 'POST'])
@endsection
