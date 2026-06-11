@extends('admin.layouts.app')
@section('title', 'Sửa banner')
@section('page-title', 'Sửa banner')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Banner', 'url' => route('admin.banners.index')],
        ['label' => 'Sửa banner'],
    ])])
@endsection
@section('content') @include('admin.banners.form', ['banner' => $banner]) @endsection
