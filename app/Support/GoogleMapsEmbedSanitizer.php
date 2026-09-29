<?php

namespace App\Support;

class GoogleMapsEmbedSanitizer
{
    /**
     * Chỉ giữ iframe nhúng Google Maps hợp lệ; loại bỏ script/HTML khác.
     */
    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        if (! preg_match(
            '/<iframe\b[^>]*\ssrc=(["\'])(https?:\/\/(?:www\.)?google\.(?:com|[a-z]{2,3})\/maps\/embed[^"\']*)\1[^>]*>\s*<\/iframe>/i',
            $html,
            $matches,
        )) {
            return '';
        }

        $src = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');

        return '<iframe src="' . $src . '" width="600" height="450" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    }

    public static function isValid(?string $html): bool
    {
        if ($html === null || trim($html) === '') {
            return true;
        }

        return self::sanitize($html) !== '';
    }
}
