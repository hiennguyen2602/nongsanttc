@extends('admin.layouts.app')
@section('title', 'Sửa người dùng')
@section('page-title', 'Sửa người dùng')
@section('content') @include('admin.users.form', ['user' => $user]) @endsection
