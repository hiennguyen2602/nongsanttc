@extends('store.layouts.app')

@section('title', 'Tin tức — ' . config('store.name'))

@section('content')
    <div class="bg-brand py-10 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <h1 class="text-2xl font-bold sm:text-3xl">Tin tức</h1>
            <p class="mt-1 text-sm text-white/80">Kiến thức nông sản & cập nhật từ {{ config('store.name') }}</p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach ($posts as $post)
                <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 transition hover:shadow-md">
                    <a href="{{ route('posts.show', $post->slug) }}">
                        <div class="aspect-[16/10] overflow-hidden">
                            <img src="{{ $post->image }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition hover:scale-105" loading="lazy">
                        </div>
                        <div class="p-5">
                            <p class="mb-2 text-xs text-slate-400">{{ $post->published_at?->format('d/m/Y') }}</p>
                            <h2 class="mb-2 line-clamp-2 font-bold text-slate-800 hover:text-brand">{{ $post->title }}</h2>
                            <p class="line-clamp-3 text-sm text-slate-600">{{ $post->excerpt }}</p>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    </div>
@endsection
