<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Support\Str;

class EditorImageService
{
    /**
     * @return array<int, string>
     */
    public function extractPaths(?string $html): array
    {
        if ($html === null || $html === '') {
            return [];
        }

        if (! preg_match_all(
            '#(?:src=["\'])(?:https?://[^"\']+/)?(uploads/editor/[a-zA-Z0-9_\-/]+\.(?:jpe?g|png|webp|gif))(?:["\'])#i',
            $html,
            $matches,
        )) {
            return [];
        }

        $paths = [];

        foreach ($matches[1] as $path) {
            $normalized = $this->normalizePath($path);

            if ($normalized !== null) {
                $paths[] = $normalized;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @return array<int, string>
     */
    public function collectReferencedPathsFromDatabase(): array
    {
        $paths = [];

        Post::query()
            ->whereNotNull('content')
            ->pluck('content')
            ->each(function (?string $content) use (&$paths) {
                $paths = array_merge($paths, $this->extractPaths($content));
            });

        Product::query()
            ->whereNotNull('description')
            ->pluck('description')
            ->each(function (?string $description) use (&$paths) {
                $paths = array_merge($paths, $this->extractPaths($description));
            });

        return array_values(array_unique($paths));
    }

    /**
     * @param  array<int, string>  $paths
     */
    public function deletePaths(array $paths, ImageUploadService $uploader): void
    {
        foreach ($paths as $path) {
            if ($this->isEditorPath($path)) {
                $uploader->delete($path);
            }
        }
    }

    public function deleteRemoved(?string $oldHtml, ?string $newHtml, ImageUploadService $uploader): void
    {
        $removed = array_diff(
            $this->extractPaths($oldHtml),
            $this->extractPaths($newHtml),
        );

        $this->deletePaths($removed, $uploader);
    }

    public function normalizePath(string $path): ?string
    {
        $path = str_replace('\\', '/', trim($path));

        if (str_contains($path, '://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = $parsed !== false && $parsed !== null ? ltrim($parsed, '/') : $path;
        }

        $path = ltrim($path, '/');

        if (! $this->isEditorPath($path)) {
            return null;
        }

        return $path;
    }

    public function isEditorPath(string $path): bool
    {
        return Str::startsWith(ltrim(str_replace('\\', '/', $path), '/'), 'uploads/editor/');
    }

    /**
     * uuid_medium.jpg → uuid.jpg (legacy variant files)
     */
    public function basePath(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (preg_match('/^(.+)_(thumbnail|medium|large)(\.[a-zA-Z0-9]+)$/', $path, $matches)) {
            return $matches[1] . $matches[3];
        }

        return $path;
    }
}
