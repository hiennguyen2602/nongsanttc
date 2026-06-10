<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quên mật khẩu — {{ config('admin.name') }}</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="admin-auth font-sans">
    <div class="w-full max-w-md">
        <div class="login-card">
            <h1 class="mb-4 text-lg font-semibold text-slate-800">Quên mật khẩu</h1>
            @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
            @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('admin.password.email') }}">
                @csrf
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" required class="form-control"></div>
                <button type="submit" class="btn-primary">Gửi link đặt lại</button>
            </form>
            <a href="{{ route('admin.login') }}" class="mt-4 block text-center text-sm text-gent-accent hover:underline">← Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>
