<?php

use App\Services\SettingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

if (! function_exists('generate_unique_slug')) {
    /**
     * Sinh slug duy nhất từ chuỗi nguồn (dùng chung cho mọi model có cột slug).
     */
    function generate_unique_slug(string $source, string $table, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = Str::slug($source);

        if ($base === '') {
            $base = 'item';
        }

        $slug = $base;
        $suffix = 1;

        while (
            DB::table($table)
                ->where($column, $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }
}

if (! function_exists('generate_unique_sku')) {
    /**
     * Sinh mã sản phẩm (SKU) duy nhất.
     */
    function generate_unique_sku(string $prefix = 'SP', string $table = 'products', string $column = 'sku'): string
    {
        do {
            $sku = $prefix . strtoupper(Str::random(8));
        } while (DB::table($table)->where($column, $sku)->exists());

        return $sku;
    }
}

if (! function_exists('store_media_has_variants')) {
    /** Ảnh upload sản phẩm có bộ thumbnail/medium/large; banner/post/settings/ảnh tĩnh thì không. */
    function store_media_has_variants(string $path): bool
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($path, 'images/')) {
            return false;
        }

        return str_starts_with($path, 'uploads/products/');
    }
}

if (! function_exists('store_media_variant_path')) {
    function store_media_variant_path(string $path, string $variant): ?string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($variant === 'original' || ! preg_match('/^(.+)\.([a-zA-Z0-9]+)$/', $path, $matches)) {
            return $path;
        }

        return $matches[1] . '_' . $variant . '.' . $matches[2];
    }
}

if (! function_exists('store_media_url')) {
    /**
     * URL for store images — supports local paths, variants, and external URLs.
     * Không gọi is_file(); tin quy ước tên file (upload SP luôn có _thumbnail/_medium/_large).
     */
    function store_media_url(?string $path, string $variant = 'medium'): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($variant !== 'original' && store_media_has_variants($path)) {
            $variantPath = store_media_variant_path($path, $variant);
            if ($variantPath !== null) {
                return asset($variantPath);
            }
        }

        return asset($path);
    }
}

if (! function_exists('store_media_variant_width')) {
    function store_media_variant_width(string $variant): int
    {
        return match ($variant) {
            'thumbnail' => 150,
            'medium' => 600,
            'large' => 1200,
            default => 600,
        };
    }
}

if (! function_exists('store_media_srcset')) {
    /**
     * @param  list<string>  $variants
     */
    function store_media_srcset(?string $path, array $variants = ['thumbnail', 'medium', 'large']): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path . ' 600w';
        }

        $resolvedVariants = store_media_has_variants($path)
            ? $variants
            : ['original'];

        $parts = [];

        foreach ($resolvedVariants as $variant) {
            $url = store_media_url($path, $variant === 'original' ? 'original' : $variant);
            $width = $variant === 'original' ? 600 : store_media_variant_width($variant);
            if ($url) {
                $parts[] = $url . ' ' . $width . 'w';
            }
        }

        return $parts !== [] ? implode(', ', array_unique($parts)) : null;
    }
}

if (! function_exists('store_media_gallery_items')) {
    /** @return list<array{thumb: ?string, display: ?string, full: ?string, srcset: ?string, fullSrcset: ?string}> */
    function store_media_gallery_items(?string $image, array $gallery = []): array
    {
        return collect(array_merge([$image], $gallery))
            ->filter(fn ($path) => filled($path))
            ->unique()
            ->map(function ($path) {
                $path = (string) $path;

                return [
                    'thumb' => store_media_url($path, 'thumbnail'),
                    'display' => store_media_url($path, 'medium'),
                    'full' => store_media_url($path, 'large'),
                    'srcset' => store_media_srcset($path),
                    'fullSrcset' => store_media_srcset($path, ['medium', 'large']),
                ];
            })
            ->values()
            ->all();
    }
}

if (! function_exists('admin_password_rule')) {
    /** Quy tắc mật khẩu admin: ≥8 ký tự, chữ hoa, chữ thường, ký tự đặc biệt. */
    function admin_password_rule(): \Illuminate\Validation\Rules\Password
    {
        return \Illuminate\Validation\Rules\Password::min(8)
            ->letters()
            ->mixedCase()
            ->symbols();
    }
}

if (! function_exists('admin_password_hint')) {
    function admin_password_hint(): string
    {
        return 'Tối thiểu 8 ký tự, gồm chữ hoa, chữ thường và ký tự đặc biệt (vd. !@#$).';
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

if (! function_exists('image_upload_max_mb')) {
    function image_upload_max_mb(): float
    {
        return (float) config('media.max_image_mb', 20);
    }
}

if (! function_exists('image_upload_max_kb')) {
    function image_upload_max_kb(): int
    {
        return (int) (image_upload_max_mb() * 1024);
    }
}

if (! function_exists('image_upload_max_label')) {
    function image_upload_max_label(): string
    {
        return rtrim(rtrim(number_format(image_upload_max_mb(), 1), '0'), '.');
    }
}

if (! function_exists('image_upload_hint')) {
    function image_upload_hint(): string
    {
        return 'Dung lượng tối đa ' . image_upload_max_label() . 'MB. Định dạng: JPG, PNG, WebP, GIF.';
    }
}

if (! function_exists('image_upload_mimes')) {
    function image_upload_mimes(): string
    {
        return implode(',', config('media.allowed_image_mimes', ['jpeg', 'jpg', 'png', 'webp', 'gif']));
    }
}

if (! function_exists('image_upload_file_rules')) {
    /** @param  list<string>  $extra  vd. ['nullable'] hoặc ['required'] */
    function image_upload_file_rules(array $extra = ['nullable']): array
    {
        return array_merge($extra, [
            'image',
            'mimes:' . image_upload_mimes(),
            'max:' . image_upload_max_kb(),
        ]);
    }
}

if (! function_exists('image_upload_validation_messages')) {
    /** @return array<string, string> */
    function image_upload_validation_messages(string $prefix = '*'): array
    {
        $maxLabel = image_upload_max_label();
        $formats = implode(', ', config('media.allowed_image_mimes', ['jpeg', 'jpg', 'png', 'webp', 'gif']));

        return [
            "{$prefix}.image" => 'File tải lên phải là hình ảnh.',
            "{$prefix}.mimes" => 'Ảnh phải có định dạng: ' . $formats . '.',
            "{$prefix}.max" => 'Ảnh không được vượt quá ' . $maxLabel . 'MB.',
            "{$prefix}.uploaded" => 'Ảnh tải lên thất bại hoặc vượt quá dung lượng cho phép (' . $maxLabel . 'MB).',
        ];
    }
}

if (! function_exists('normalize_upload_path')) {
    function normalize_upload_path(string $path): string
    {
        return str_replace('\\', '/', trim($path, '/'));
    }
}

if (! function_exists('is_valid_upload_path')) {
    /** Kiểm path upload an toàn (không .., đúng thư mục uploads/, đuôi ảnh). */
    function is_valid_upload_path(string $path, ?string $prefix = null): bool
    {
        $path = normalize_upload_path($path);

        if ($path === '' || str_contains($path, '..')) {
            return false;
        }

        if (! preg_match('#^uploads/[a-zA-Z0-9_\-/]+\.(jpe?g|png|webp|gif)$#i', $path)) {
            return false;
        }

        if ($prefix !== null) {
            $prefix = rtrim(normalize_upload_path($prefix), '/');
            if (! str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('kept_upload_paths')) {
    /**
     * Lọc danh sách path client gửi — chỉ giữ path thuộc whitelist entity hiện tại.
     *
     * @param  list<mixed>  $submitted
     * @param  list<string|null>  $allowed
     * @return list<string>
     */
    function kept_upload_paths(array $submitted, array $allowed, string $prefix, string $errorField = 'images'): array
    {
        $allowedMap = [];
        foreach (array_filter($allowed) as $path) {
            $allowedMap[normalize_upload_path($path)] = true;
        }

        $kept = [];

        foreach ($submitted as $path) {
            if (! is_string($path) || ! filled($path)) {
                continue;
            }

            $normalized = normalize_upload_path($path);

            if (! is_valid_upload_path($normalized, $prefix)) {
                throw ValidationException::withMessages([
                    $errorField => 'Đường dẫn ảnh không hợp lệ.',
                ]);
            }

            if (! isset($allowedMap[$normalized])) {
                throw ValidationException::withMessages([
                    $errorField => 'Ảnh giữ lại không thuộc bản ghi này.',
                ]);
            }

            $kept[] = $normalized;
        }

        return array_values(array_unique($kept));
    }
}

if (! function_exists('resolve_kept_upload_path')) {
    /**
     * Xác nhận ảnh đại diện giữ lại — path phải khớp DB, không tin client tùy ý.
     *
     * @return string|null  path giữ lại, hoặc null nếu client không gửi existing
     */
    function resolve_kept_upload_path(?string $submitted, ?string $current, string $prefix, string $errorField = 'image'): ?string
    {
        if (! filled($submitted)) {
            return null;
        }

        $submittedNormalized = normalize_upload_path($submitted);

        if (! is_valid_upload_path($submittedNormalized, $prefix)) {
            throw ValidationException::withMessages([
                $errorField => 'Đường dẫn ảnh không hợp lệ.',
            ]);
        }

        if (! filled($current)) {
            throw ValidationException::withMessages([
                $errorField => 'Ảnh giữ lại không hợp lệ.',
            ]);
        }

        $currentNormalized = normalize_upload_path($current);

        if ($submittedNormalized !== $currentNormalized) {
            throw ValidationException::withMessages([
                $errorField => 'Ảnh giữ lại không khớp với ảnh hiện tại.',
            ]);
        }

        return $currentNormalized;
    }
}

if (! function_exists('env_nullable_string')) {
    /** Coi chuỗi rỗng hoặc "null" trong .env thành null thật (tránh cookie Domain=null). */
    function env_nullable_string(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;

        return strtolower($value) === 'null' ? null : $value;
    }
}

if (! function_exists('env_bool_default')) {
    function env_bool_default(mixed $value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
