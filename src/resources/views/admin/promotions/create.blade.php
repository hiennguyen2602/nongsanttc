@extends('admin.layouts.app')
@section('title', 'Thêm khuyến mãi')
@section('page-title', 'Thêm khuyến mãi')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Khuyến mãi', 'url' => route('admin.promotions.index')],
        ['label' => 'Thêm khuyến mãi'],
    ])])
@endsection
@section('content') @include('admin.promotions.form') @endsection
