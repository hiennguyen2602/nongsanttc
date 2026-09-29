@extends('admin.layouts.app')
@section('title', 'Thêm bài viết')
@section('page-title', 'Thêm bài viết')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Bài viết', 'url' => route('admin.posts.index')],
        ['label' => 'Thêm bài viết'],
    ])])
@endsection
@section('content')
    @include('admin.posts.form')
@endsection
