@php
    $items = [];
    $position = 1;
    foreach ($crumbs as $crumb) {
        $item = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $crumb['name'],
        ];
        if (! empty($crumb['url'])) {
            $item['item'] = $crumb['url'];
        }
        $items[] = $item;
    }
@endphp
@include('partials.seo.json-ld', ['data' => [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $items,
]])
