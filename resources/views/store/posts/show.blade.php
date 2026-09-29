@extends('store.layouts.app')

@php
    $postShareUrl = route('posts.show', $post->slug, absolute: true);
    $postOgImage = store_media_url($post->image, 'large');
    $postTitle = filled($post->meta_title) ? trim($post->meta_title) : $post->title;
    $postDescription = seo_entity_description($post->excerpt ?: $post->content, $post->meta_description);
@endphp

@section('title', seo_entity_title($post->title, $post->meta_title))
@section('meta_description', $postDescription)
@section('canonical', $postShareUrl)
@section('og_type', 'article')
@section('og_title', $postTitle)
@section('og_description', $postDescription)
@section('og_url', $postShareUrl)
@section('og_image_alt', $postTitle)
@if ($postOgImage)
    @section('og_image', $postOgImage)
@endif

@push('head')
    @if ($post->published_at)
        <meta property="article:published_time" content="{{ $post->published_at->toAtomString() }}">
    @endif
    <meta property="article:modified_time" content="{{ $post->updated_at->toAtomString() }}">
@endpush

@push('json-ld')
    @include('partials.seo.json-ld', ['data' => [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => route('home', absolute: true)],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tin tức', 'item' => route('posts.index', absolute: true)],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $postShareUrl],
        ],
    ]])
    @php
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $postDescription,
            'url' => $postShareUrl,
            'image' => $postOgImage,
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => $post->updated_at->toAtomString(),
            'author' => ['@type' => 'Organization', 'name' => store_setting('name')],
            'publisher' => [
                '@type' => 'Organization',
                'name' => store_setting('name'),
                'logo' => store_media_url(store_setting('hero_desktop'), 'large') ? [
                    '@type' => 'ImageObject',
                    'url' => store_media_url(store_setting('hero_desktop'), 'large'),
                ] : null,
            ],
        ];
    @endphp
    @include('partials.seo.json-ld', ['data' => array_filter($articleSchema, fn ($value) => $value !== null && $value !== '')])
@endpush

@section('content')
    <article class="store-container store-container--article py-10">
        <nav class="mb-6 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-brand">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('posts.index') }}" class="hover:text-brand">Tin tức</a>
        </nav>

        @include('partials.post-article-header', ['post' => $post])

        @include('partials.rich-content', ['html' => $post->content])

        @include('store.partials.share-buttons', [
            'shareUrl' => $postShareUrl,
            'shareTitle' => $post->title,
            'wrapperClass' => 'mt-8 border-t border-slate-200 pt-8',
        ])
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
