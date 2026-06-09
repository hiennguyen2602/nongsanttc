@extends('admin.layouts.app')
@section('title', 'Sửa bài viết')
@section('page-title', 'Sửa bài viết')
@section('content')
    @include('admin.posts.form', ['post' => $post])
@endsection
