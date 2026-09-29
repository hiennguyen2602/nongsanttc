@php
    $seoTitle = trim($__env->yieldContent('title'));
    $seoDescription = trim($__env->yieldContent('meta_description')) ?: store_setting('tagline');
    $seoCanonical = trim($__env->yieldContent('canonical')) ?: seo_absolute_url();
    $seoRobots = trim($__env->yieldContent('robots')) ?: 'index,follow';
    $seoOgType = trim($__env->yieldContent('og_type')) ?: 'website';
    $seoOgTitle = trim($__env->yieldContent('og_title')) ?: $seoTitle;
    $seoOgDescription = trim($__env->yieldContent('og_description')) ?: $seoDescription;
    $seoOgUrl = trim($__env->yieldContent('og_url')) ?: $seoCanonical;
    $seoOgImage = trim($__env->yieldContent('og_image')) ?: store_media_url(store_setting('hero_desktop'), 'large');
    $seoOgImageSecure = $seoOgImage ? preg_replace('/^http:/', 'https:', $seoOgImage) : null;
    $seoOgImageAlt = trim($__env->yieldContent('og_image_alt')) ?: $seoOgTitle;
    $googleSiteVerification = trim((string) store_setting('google_site_verification', ''));
@endphp

@if ($googleSiteVerification !== '')
    <meta name="google-site-verification" content="{{ $googleSiteVerification }}">
@endif

<link rel="canonical" href="{{ $seoCanonical }}">
<meta name="robots" content="{{ $seoRobots }}">

<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="{{ $seoOgType }}">
<meta property="og:site_name" content="{{ store_setting('name') }}">
<meta property="og:title" content="{{ $seoOgTitle }}">
<meta property="og:description" content="{{ $seoOgDescription }}">
<meta property="og:url" content="{{ $seoOgUrl }}">
@if ($seoOgImage)
    <meta property="og:image" content="{{ $seoOgImage }}">
    @if ($seoOgImageSecure)
        <meta property="og:image:secure_url" content="{{ $seoOgImageSecure }}">
    @endif
    <meta property="og:image:alt" content="{{ $seoOgImageAlt }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoOgTitle }}">
<meta name="twitter:description" content="{{ $seoOgDescription }}">
@if ($seoOgImage)
    <meta name="twitter:image" content="{{ $seoOgImage }}">
@endif
