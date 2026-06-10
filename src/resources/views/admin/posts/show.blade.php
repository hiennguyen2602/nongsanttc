@extends('admin.layouts.app')

@section('title', $post->title)
@section('page-title', 'Chi tiết bài viết')

@section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2>{{ $post->title }}</h2>
            <div class="form-actions" style="margin:0">
                @if ($post->is_published)
                    <a href="{{ route('posts.show', $post->slug) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">Xem trên web</a>
                @endif
                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary btn-sm">Sửa</a>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
            </div>
        </div>
        <div class="x_content">
            <article class="admin-post-preview mx-auto max-w-4xl">
                <header class="mb-8">
                    <p class="mb-2 text-sm text-slate-500">{{ $post->published_at?->format('d/m/Y') }}</p>
                    @include('admin.partials.status-badge', ['label' => $post->publishStatusLabel(), 'class' => $post->publishStatusBadgeClass()])
                    <h1 class="mt-4 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $post->title }}</h1>
                </header>

                @if ($post->excerpt)
                    <p class="mb-8 text-lg leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
                @endif

                @if ($post->content)
                    <div class="prose prose-slate max-w-none text-slate-700">{!! $post->content !!}</div>
                @endif
            </article>
        </div>
    </div>
@endsection
