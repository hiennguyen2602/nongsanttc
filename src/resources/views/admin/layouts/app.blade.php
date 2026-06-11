<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('admin.name') }}</title>
    @include('partials.favicon')
    @vite(['resources/css/admin.css', 'resources/scss/pagination.scss', 'resources/js/admin.js'])
</head>
<body class="admin-body bg-gent-content font-sans text-black antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')

        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            @include('admin.partials.topbar')

            <main class="admin-main flex-1 p-4 sm:p-6" role="main">
                <div class="admin-page-frame">
                    @include('admin.partials.alerts')
                    @yield('content')
                </div>
            </main>

            <footer class="border-t border-slate-200 bg-white px-4 py-3 text-center text-xs text-slate-500 sm:px-6">
                &copy; {{ date('Y') }} {{ config('admin.name') }}
            </footer>
        </div>
    </div>

    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-black/50 lg:hidden"
        style="display: none;"
        aria-hidden="true"
    ></div>
</body>
</html>
