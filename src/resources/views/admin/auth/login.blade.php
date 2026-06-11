<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Đăng nhập Admin — {{ config('admin.name') }}</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="admin-auth font-sans">
    <div class="admin-auth__shell">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gent-accent text-lg font-bold text-white">TTC</div>
            <h1 class="text-xl font-semibold text-white">{{ config('admin.name') }}</h1>
            <p class="mt-1 text-sm text-slate-400">Đăng nhập quản trị hệ thống</p>
        </div>

        <div class="login-card">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="form-control" placeholder="admin@nongsanttc.local">
                </div>

                <div class="mb-3">
                    <div class="mb-1 flex items-center justify-between">
                        <label for="password" class="form-label mb-0">Mật khẩu</label>
                        <a href="{{ route('admin.password.forgot') }}" class="text-xs text-gent-accent hover:underline">Quên mật khẩu?</a>
                    </div>
                    <input type="password" name="password" id="password" required autocomplete="new-password" class="form-control">
                </div>

                <label class="mb-4 flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="form-check-input">
                    Ghi nhớ đăng nhập
                </label>

                <button type="submit" class="btn-primary">Đăng nhập</button>
            </form>
        </div>
    </div>
</body>
</html>
