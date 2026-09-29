@php
    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => store_setting('name'),
        'url' => route('home', absolute: true),
        'description' => store_setting('tagline'),
        'logo' => store_media_url(store_setting('hero_desktop'), 'large'),
    ];
    if (filled(store_setting('phone'))) {
        $organization['telephone'] = store_setting('phone');
    }
    if (filled(store_setting('email'))) {
        $organization['email'] = store_setting('email');
    }
    if (filled(store_setting('address'))) {
        $organization['address'] = [
            '@type' => 'PostalAddress',
            'streetAddress' => store_setting('address'),
            'addressCountry' => 'VN',
        ];
    }
@endphp
@include('partials.seo.json-ld', ['data' => array_filter($organization, fn ($value) => $value !== null && $value !== '')])
