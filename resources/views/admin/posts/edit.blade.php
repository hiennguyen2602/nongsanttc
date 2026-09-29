@extends('admin.layouts.app')
@section('title', 'Sửa bài viết')
@section('page-title', 'Sửa bài viết')
@section('breadcrumbs')
    @include('admin.partials.breadcrumb', ['items' => admin_breadcrumb([
        ['label' => 'Bài viết', 'url' => route('admin.posts.index')],
        ['label' => 'Chi tiết bài viết', 'url' => route('admin.posts.show', $post)],
        ['label' => 'Sửa bài viết'],
    ])])
@endsection
@section('content')
    @include('admin.posts.form', ['post' => $post])
@endsection
