@extends('admin.layouts.app')
@section('title', 'Sửa khuyến mãi')
@section('page-title', 'Sửa khuyến mãi')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Khuyến mãi', 'url' => route('admin.promotions.index')],
        ['label' => 'Sửa khuyến mãi'],
    ])])
@endsection
@section('content') @include('admin.promotions.form', ['promotion' => $promotion]) @endsection
