@extends('admin.layouts.app')
@section('title', 'Sửa người dùng')
@section('page-title', 'Sửa người dùng')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Người dùng', 'url' => route('admin.users.index')],
        ['label' => 'Sửa người dùng'],
    ])])
@endsection
@section('content') @include('admin.users.form', ['user' => $user]) @endsection
