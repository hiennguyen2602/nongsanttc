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
        );

        return response()->json([
            'url' => $result['url'],
            'path' => $result['path'],
        ]);
    }
}
