@extends('admin.layouts.app')
@section('title', 'Đổi mật khẩu')
@section('page-title', 'Đổi mật khẩu')
@section('content')
    <div class="x_panel">
        <div class="x_title"><h2>Đổi mật khẩu</h2></div>
        <div class="x_content">
            <form method="POST" action="{{ route('admin.password.change.submit') }}" class="admin-form-narrow">
                @csrf
                <div class="mb-3"><label class="form-label">Mật khẩu hiện tại</label><input type="password" name="current_password" required class="form-control"></div>
                <div class="mb-3"><label class="form-label">Mật khẩu mới</label><input type="password" name="password" required class="form-control"></div>
                <div class="mb-4"><label class="form-label">Xác nhận mật khẩu mới</label><input type="password" name="password_confirmation" required class="form-control"></div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
@endsection
