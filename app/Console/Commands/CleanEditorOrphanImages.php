<?php

namespace App\Console\Commands;

use App\Services\EditorImageService;
use App\Services\ImageUploadService;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class CleanEditorOrphanImages extends Command
{
    protected $signature = 'media:clean-editor-orphans {--hours= : Số giờ grace trước khi xóa file mồ côi}';

    protected $description = 'Xóa ảnh trong uploads/editor không còn được tham chiếu trong DB';

    public function handle(EditorImageService $editorImages, ImageUploadService $uploader): int
    {
        $hours = (int) ($this->option('hours') ?: config('media.editor_orphan_grace_hours', 48));
        $cutoff = now()->subHours($hours)->getTimestamp();

        $referenced = array_fill_keys($editorImages->collectReferencedPathsFromDatabase(), true);
        $editorRoot = public_path('uploads/editor');

        if (! is_dir($editorRoot)) {
            $this->info('Thư mục uploads/editor không tồn tại.');

            return self::SUCCESS;
        }

        $deleted = 0;
        $seenBases = [];

        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($editorRoot)) as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen(public_path()) + 1));

            if (! $editorImages->isEditorPath($relative)) {
                continue;
            }

            $base = $editorImages->basePath($relative);

            if (isset($referenced[$base])) {
                continue;
            }

            if ($file->getMTime() > $cutoff) {
                continue;
            }

            if (isset($seenBases[$base])) {
                continue;
            }

            $seenBases[$base] = true;
            $uploader->delete($base);
            $deleted++;
        }

        $this->info("Đã xóa {$deleted} ảnh editor mồ côi (grace: {$hours} giờ).");

        return self::SUCCESS;
    }
}
