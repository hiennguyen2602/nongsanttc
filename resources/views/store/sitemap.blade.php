<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($staticPages as $page)
    <url>
        <loc>{{ $page['loc'] }}</loc>
        @if ($page['lastmod'])
            <lastmod>{{ \Illuminate\Support\Carbon::parse($page['lastmod'])->toAtomString() }}</lastmod>
        @endif
    </url>
@endforeach
@foreach ($products as $product)
    <url>
        <loc>{{ route('products.show', $product->slug, absolute: true) }}</loc>
        <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
    </url>
@endforeach
@foreach ($posts as $post)
    <url>
        <loc>{{ route('posts.show', $post->slug, absolute: true) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
    </url>
@endforeach
</urlset>
