@props(['post'])

<article {{ $attributes->merge(['class' => 'post-card-hover group h-full overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80']) }}>
    <a href="{{ route('posts.show', $post->slug) }}" class="flex h-full flex-col">
        <div class="aspect-[16/10] overflow-hidden bg-slate-100">
            <img
                src="{{ store_media_url($post->image) }}"
                alt="{{ $post->title }}"
                class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
                loading="lazy"
            >
        </div>
        <div class="flex flex-1 flex-col p-6">
            <time class="text-xs font-medium uppercase tracking-wider text-brand">{{ $post->published_at?->format('d/m/Y') }}</time>
            <h3 class="mt-2 mb-2 line-clamp-2 text-lg font-bold text-slate-800 transition-colors duration-300 group-hover:text-brand">{{ $post->title }}</h3>
            <p class="line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
            <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand opacity-0 transition duration-300 group-hover:opacity-100">
                Đọc thêm →
            </span>
        </div>
    </a>
</article>
