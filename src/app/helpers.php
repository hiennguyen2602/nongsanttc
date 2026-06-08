<?php

if (! function_exists('store_media_url')) {
    /**
     * URL for store images — supports local paths (images/store/...) and external URLs.
     */
    function store_media_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}
