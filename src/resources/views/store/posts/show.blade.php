@extends('store.layouts.app')

@section('title', $post->title . ' — ' . config('store.name'))

@section('content')
    <article class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
        <nav class="mb-6 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-brand">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('posts.index') }}" class="hover:text-brand">Tin tức</a>
        </nav>

        <header class="mb-8">
            <p class="mb-2 text-sm text-slate-400">{{ $post->published_at?->format('d/m/Y') }}</p>
            <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">{{ $post->title }}</h1>
        </header>

        <div class="prose prose-slate max-w-none text-slate-700">
            {!! $post->content !!}
        </div>
    </article>

    @if ($recentPosts->isNotEmpty())
        <section class="border-t border-slate-200 bg-slate-50 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <h2 class="mb-8 text-center text-2xl font-bold text-slate-900 sm:text-3xl">Bài viết khác</h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach ($recentPosts as $recent)
                        <article class="group overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 transition hover:-translate-y-1 hover:shadow-xl">
                            <a href="{{ route('posts.show', $recent->slug) }}">
                                <div class="aspect-[16/10] overflow-hidden">
                                    <img src="{{ store_media_url($recent->image) }}" alt="{{ $recent->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                </div>
                                <div class="p-6">
                                    <time class="text-xs font-medium uppercase tracking-wider text-brand">{{ $recent->published_at?->format('d/m/Y') }}</time>
                                    <h3 class="mt-2 mb-2 line-clamp-2 text-lg font-bold text-slate-800 transition group-hover:text-brand">{{ $recent->title }}</h3>
                                    <p class="line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $recent->excerpt }}</p>
                                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand opacity-0 transition group-hover:opacity-100">
                                        Đọc thêm →
                                    </span>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
