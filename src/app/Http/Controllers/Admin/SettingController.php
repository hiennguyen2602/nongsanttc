<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImageUploadService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(SettingService $settings): View
    {
        $groups = Setting::query()->orderBy('group')->orderBy('id')->get()->groupBy('group');

        return view('admin.settings.edit', compact('groups'));
    }

    public function update(Request $request, SettingService $settings, ImageUploadService $uploader): RedirectResponse
    {
        $items = Setting::query()->get();

        foreach ($items as $item) {
            $key = $item->key;

            if ($item->type === 'image') {
                if ($request->hasFile($key)) {
                    $preset = in_array($key, ['hero_desktop', 'hero_mobile'], true) ? $key : null;
                    $folder = 'uploads/settings/' . date('Y/m');
                    $result = $uploader->upload($request->file($key), $folder, $preset);
                    $settings->set($key, $result['path']);
                }

                continue;
            }

            if ($request->has($key)) {
                $settings->set($key, $request->input($key));
            }
        }

        return back()->with('success', 'Cập nhật cài đặt thành công.');
    }
}
