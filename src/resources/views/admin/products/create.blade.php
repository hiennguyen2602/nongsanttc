@extends('admin.layouts.app')
@section('title', 'Thêm sản phẩm')
@section('page-title', 'Thêm sản phẩm')
@section('content')
    @include('admin.products._form', ['action' => route('admin.products.store'), 'method' => 'POST'])
@endsection
