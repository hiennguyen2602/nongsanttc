@extends('admin.layouts.app')
@section('title', 'Thêm banner')
@section('page-title', 'Thêm banner')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Banner', 'url' => route('admin.banners.index')],
        ['label' => 'Thêm banner'],
    ])])
@endsection
@section('content') @include('admin.banners.form') @endsection
