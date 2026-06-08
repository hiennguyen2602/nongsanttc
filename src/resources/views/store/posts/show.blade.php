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

        @if ($post->image)
            <img src="{{ store_media_url($post->image) }}" alt="{{ $post->title }}" class="mb-8 aspect-[16/9] w-full rounded-xl object-cover">
        @endif

        <div class="prose prose-slate max-w-none text-slate-700">
            {!! $post->content !!}
        </div>
    </article>

    @if ($recentPosts->isNotEmpty())
        <section class="border-t border-slate-200 bg-slate-50 py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <h2 class="mb-6 text-lg font-bold">Bài viết khác</h2>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($recentPosts as $recent)
                        <a href="{{ route('posts.show', $recent->slug) }}" class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200 hover:shadow-md">
                            <p class="line-clamp-2 font-medium text-slate-800 hover:text-brand">{{ $recent->title }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $recent->published_at?->format('d/m/Y') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
