<?php

use App\Services\SettingService;

if (! function_exists('store_media_url')) {
    /**
     * URL for store images — supports local paths, variants, and external URLs.
     */
    function store_media_url(?string $path, string $variant = 'medium'): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if ($variant !== 'original' && preg_match('/^(.+)\.([a-zA-Z0-9]+)$/', $path, $matches)) {
            $variantPath = $matches[1] . '_' . $variant . '.' . $matches[2];
            if (is_file(public_path($variantPath))) {
                return asset($variantPath);
            }
        }

        return asset($path);
    }
}

if (! function_exists('store_setting')) {
    function store_setting(string $key, mixed $default = null): mixed
    {
        static $settings = null;

        if ($settings === null) {
            $settings = app(SettingService::class)->all();
        }

        return $settings[$key] ?? config('store.' . $key, $default);
    }
}

if (! function_exists('format_money')) {
    function format_money(int $amount): string
    {
        return number_format($amount, 0, ',', '.') . 'đ';
    }
}
