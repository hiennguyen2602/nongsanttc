<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt lại mật khẩu — {{ config('admin.name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/admin.css'])
</head>
<body class="admin-auth font-sans">
    <div class="w-full max-w-md">
        <div class="login-card">
            <h1 class="mb-4 text-lg font-semibold text-slate-800">Đặt lại mật khẩu</h1>
            @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $email) }}" required class="form-control"></div>
                <div class="mb-3"><label class="form-label">Mật khẩu mới</label><input type="password" name="password" required class="form-control"></div>
                <div class="mb-4"><label class="form-label">Xác nhận mật khẩu</label><input type="password" name="password_confirmation" required class="form-control"></div>
                <button type="submit" class="btn-primary">Đặt lại mật khẩu</button>
            </form>
        </div>
    </div>
</body>
</html>
