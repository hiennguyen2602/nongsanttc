@extends('admin.layouts.app')
@section('title', 'Thêm người dùng')
@section('page-title', 'Thêm người dùng')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Người dùng', 'url' => route('admin.users.index')],
        ['label' => 'Thêm người dùng'],
    ])])
@endsection
@section('content') @include('admin.users.form') @endsection
