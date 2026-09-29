<?php

namespace App\Rules;

use App\Support\GoogleMapsEmbedSanitizer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidGoogleMapsEmbed implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || trim((string) $value) === '') {
            return;
        }

        if (! GoogleMapsEmbedSanitizer::isValid((string) $value)) {
            $fail('Mã nhúng bản đồ không hợp lệ. Chỉ chấp nhận iframe Google Maps.');
        }
    }
}
