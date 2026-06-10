<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function upload(Request $request, ImageUploadService $uploader): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ]);

        $result = $uploader->upload(
            $request->file('file'),
            'uploads/editor/' . date('Y/m'),
            null,
            (int) config('media.editor_max_width', 1200),
        );

        return response()->json([
            'url' => $result['url'],
            'path' => $result['path'],
        ]);
    }

    public function destroy(Request $request, ImageUploadService $uploader): JsonResponse
    {
        $path = ltrim((string) $request->input('path'), '/');

        // Chỉ cho phép xóa ảnh trong thư mục editor để tránh xóa nhầm file khác.
        if ($path === '' || ! str_starts_with($path, 'uploads/editor/')) {
            return response()->json(['deleted' => false], 422);
        }

        $uploader->delete($path);

        return response()->json(['deleted' => true]);
    }
}
