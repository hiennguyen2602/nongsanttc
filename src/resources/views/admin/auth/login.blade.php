<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Admin — {{ config('admin.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gent-sidebar font-sans">
    <div class="w-full max-w-md px-4">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gent-accent text-lg font-bold text-white">
                TTC
            </div>
            <h1 class="text-xl font-semibold text-white">{{ config('admin.name') }}</h1>
            <p class="mt-1 text-sm text-slate-400">Đăng nhập quản trị hệ thống</p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow-xl sm:p-8">
            @if ($errors->any())
                <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-gent-accent focus:outline-none focus:ring-2 focus:ring-gent-accent/20"
                        placeholder="admin@nongsanttc.local"
                    >
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Mật khẩu</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-gent-accent focus:outline-none focus:ring-2 focus:ring-gent-accent/20"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-gent-accent focus:ring-gent-accent">
                    Ghi nhớ đăng nhập
                </label>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-gent-accent py-2.5 text-sm font-semibold text-white transition hover:bg-gent-accent-dark"
                >
                    Đăng nhập
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            Layout admin theo
            <a href="https://preview.colorlib.com/theme/gentelella/production/index.html" class="text-gent-accent hover:underline" target="_blank">Gentelella</a>
            — Tailwind CSS
        </p>
    </div>
</body>
</html>
