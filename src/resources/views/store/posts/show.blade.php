@extends('store.layouts.app')

@section('title', $post->title . ' — ' . store_setting('name'))

@section('content')
    <article class="store-container store-container--article py-10">
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
            <div class="store-container">
                <h2 class="mb-8 text-center text-2xl font-bold text-slate-900 sm:text-3xl">Bài viết khác</h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach ($recentPosts as $recent)
                        @include('store.partials.post-card', ['post' => $recent])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
