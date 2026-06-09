@extends('admin.layouts.app')
@section('title', 'Sửa sản phẩm')
@section('page-title', 'Sửa sản phẩm')
@section('content')
    @include('admin.products._form', ['action' => route('admin.products.update', $product), 'method' => 'PUT', 'product' => $product])
@endsection
