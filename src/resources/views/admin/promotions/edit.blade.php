@extends('admin.layouts.app')
@section('title', 'Sửa khuyến mãi')
@section('page-title', 'Sửa khuyến mãi')
@section('content') @include('admin.promotions.form', ['promotion' => $promotion]) @endsection
