<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', store_setting('name'))</title>
    <meta name="description" content="@yield('meta_description', store_setting('tagline'))">
    @include('partials.favicon')
    {{-- Chữ header trắng trước khi Tailwind/Alpine load — không set background ở đây (sẽ chặn bg-brand) --}}
    <style>.site-header,.site-header a,.site-header button{color:#fff}</style>
    @vite(['resources/css/app.css', 'resources/scss/pagination.scss', 'resources/js/store.js'])
    @stack('head')
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-800 antialiased">
    @include('store.partials.header')

    <main class="flex-1 pb-24 sm:pb-28">
        @yield('content')
    </main>

    @include('store.partials.footer')
    @include('store.partials.floating-contact')
    @include('store.partials.scroll-top')

    @stack('scripts')
</body>
</html>
