<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class ImageUploadService
{
    /** @var array<string, int> */
    private const VARIANTS = [
        'thumbnail' => 150,
        'medium' => 600,
        'large' => 1200,
    ];

    /** Giới hạn chiều rộng ảnh gốc (bộ variants đầy đủ). */
    private const ORIGINAL_MAX_WIDTH = 1600;

    /** @var array<string, array{width: int, height: int}> */
    private const PRESETS = [
        'hero_desktop' => ['width' => 1920, 'height' => 700],
        'hero_mobile' => ['width' => 768, 'height' => 500],
        'banner' => ['width' => 1200, 'height' => 400],
    ];

    /**
     * @return array{path: string, url: string, variants: array<string, string>}
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', ?string $preset = null, ?int $singleMaxWidth = null): array
    {
        $this->validate($file);

        $directory = public_path(trim($folder, '/'));
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Không thể tạo thư mục upload.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $basename = Str::uuid()->toString();
        $filename = $basename . '.' . $extension;
        $relativePath = trim($folder, '/') . '/' . $filename;
        $absolutePath = $directory . '/' . $filename;

        $file->move($directory, $filename);

        $variants = [];

        if ($preset && isset(self::PRESETS[$preset])) {
            $this->cropToPreset($absolutePath, self::PRESETS[$preset]);
            $variants['original'] = $relativePath;
        } elseif ($singleMaxWidth !== null) {
            $this->resizeImage($absolutePath, $absolutePath, $singleMaxWidth);
            $variants['original'] = $relativePath;
        } else {
            foreach (self::VARIANTS as $name => $maxWidth) {
                $variantFilename = $basename . '_' . $name . '.' . $extension;
                $variantRelative = trim($folder, '/') . '/' . $variantFilename;
                $this->resizeImage($absolutePath, $directory . '/' . $variantFilename, $maxWidth);
                $variants[$name] = $variantRelative;
            }

            $this->resizeImage($absolutePath, $absolutePath, self::ORIGINAL_MAX_WIDTH);

            $variants['original'] = $relativePath;
        }

        return [
            'path' => $relativePath,
            'url' => asset($relativePath),
            'variants' => $variants,
        ];
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $paths = [$path];

        if (preg_match('/^(.+)\.([a-zA-Z0-9]+)$/', $path, $matches)) {
            foreach (array_keys(self::VARIANTS) as $variant) {
                $paths[] = $matches[1] . '_' . $variant . '.' . $matches[2];
            }
        }

        foreach ($paths as $filePath) {
            $absolute = public_path(ltrim($filePath, '/'));
            if (is_file($absolute)) {
                unlink($absolute);
            }
        }
    }

    private function validate(UploadedFile $file): void
    {
        if (! in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            throw new RuntimeException('Định dạng ảnh không hợp lệ.');
        }

        $maxMb = (float) config('media.max_image_mb', 5);
        if ($file->getSize() > $maxMb * 1024 * 1024) {
            throw new RuntimeException('Ảnh không được vượt quá ' . rtrim(rtrim(number_format($maxMb, 1), '0'), '.') . 'MB.');
        }
    }

    /**
     * @param  array{width: int, height: int}  $preset
     */
    private function cropToPreset(string $sourcePath, array $preset): void
    {
        [$width, $height, $type] = $this->loadImageInfo($sourcePath);
        $source = $this->createImageFromFile($sourcePath, $type);

        $targetRatio = $preset['width'] / $preset['height'];
        $sourceRatio = $width / $height;

        if ($sourceRatio > $targetRatio) {
            $newWidth = (int) round($height * $targetRatio);
            $srcX = (int) (($width - $newWidth) / 2);
            $srcY = 0;
            $cropW = $newWidth;
            $cropH = $height;
        } else {
            $newHeight = (int) round($width / $targetRatio);
            $srcX = 0;
            $srcY = (int) (($height - $newHeight) / 2);
            $cropW = $width;
            $cropH = $newHeight;
        }

        $cropped = imagecreatetruecolor($preset['width'], $preset['height']);
        imagecopyresampled(
            $cropped,
            $source,
            0,
            0,
            $srcX,
            $srcY,
            $preset['width'],
            $preset['height'],
            $cropW,
            $cropH,
        );

        $this->saveImage($cropped, $sourcePath, $type);
        imagedestroy($source);
        imagedestroy($cropped);
    }

    private function resizeImage(string $sourcePath, string $destPath, int $maxWidth): void
    {
        [$width, $height, $type] = $this->loadImageInfo($sourcePath);

        if ($width <= $maxWidth) {
            if ($sourcePath !== $destPath) {
                copy($sourcePath, $destPath);
            }

            return;
        }

        $newWidth = $maxWidth;
        $newHeight = (int) round($height * ($maxWidth / $width));

        $source = $this->createImageFromFile($sourcePath, $type);
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $this->saveImage($resized, $destPath, $type);
        imagedestroy($source);
        imagedestroy($resized);
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function loadImageInfo(string $path): array
    {
        $info = getimagesize($path);
        if ($info === false) {
            throw new RuntimeException('Không đọc được file ảnh.');
        }

        return [$info[0], $info[1], $info[2]];
    }

    private function createImageFromFile(string $path, int $type)
    {
        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            default => throw new RuntimeException('Định dạng ảnh không được hỗ trợ.'),
        };
    }

    private function saveImage($image, string $path, int $type): void
    {
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($image, $path, 85),
            IMAGETYPE_PNG => imagepng($image, $path, 6),
            IMAGETYPE_WEBP => imagewebp($image, $path, 85),
            IMAGETYPE_GIF => imagegif($image, $path),
            default => throw new RuntimeException('Định dạng ảnh không được hỗ trợ.'),
        };
    }
}
