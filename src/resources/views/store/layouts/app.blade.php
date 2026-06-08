<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('store.name'))</title>
    <meta name="description" content="@yield('meta_description', config('store.tagline'))">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white font-sans text-slate-800 antialiased">
    @include('store.partials.header')

    <main>
        @yield('content')
    </main>

    @include('store.partials.footer')
    @include('store.partials.floating-contact')
    @include('store.partials.scroll-top')

    @stack('scripts')
</body>
</html>
