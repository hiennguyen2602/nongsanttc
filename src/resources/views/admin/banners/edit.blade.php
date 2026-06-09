@extends('admin.layouts.app')
@section('title', 'Sửa banner')
@section('page-title', 'Sửa banner')
@section('content') @include('admin.banners.form', ['banner' => $banner]) @endsection
