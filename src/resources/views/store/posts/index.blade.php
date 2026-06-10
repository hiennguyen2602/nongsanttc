@extends('store.layouts.app')

@section('title', 'Tin tức — ' . store_setting('name'))

@section('content')
    <div class="bg-brand py-10 text-white">
        <div class="store-container">
            <h1 class="text-2xl font-bold sm:text-3xl">Tin tức</h1>
            <p class="mt-1 text-sm text-white/80">Kiến thức nông sản & cập nhật từ {{ store_setting('name') }}</p>
        </div>
    </div>

    <div class="store-container py-10">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach ($posts as $post)
                @include('store.partials.post-card', ['post' => $post])
            @endforeach
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    </div>
@endsection
