<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\ImageUploadService;
use App\Services\SettingService;
use App\Support\GoogleMapsEmbedSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use RuntimeException;

class SettingController extends Controller
{
    /** @var list<string> */
    private const GROUP_ORDER = [
        'general',
        'seo',
        'contact',
        'social',
        'banner',
    ];

    /** @var array<string, list<string>> */
    private const GROUP_KEY_ORDER = [
        'seo' => [
            'google_site_verification',
            'about_meta_description',
            'contact_meta_description',
        ],
        'contact' => [
            'company_name',
            'phone',
            'email',
            'address',
            'google_maps_url',
            'google_maps_embed',
        ],
        'social' => [
            'zalo',
            'facebook',
            'messenger',
            'youtube',
            'tiktok',
        ],
        'banner' => [
            'hero_desktop',
            'hero_mobile',
            'about_main',
            'about_small',
        ],
    ];

    /** @var array<string, string> */
    private const GROUP_LABELS = [
        'general' => 'Chung',
        'seo' => 'SEO',
        'contact' => 'Contact',
        'social' => 'Social',
        'banner' => 'Banner',
    ];

    /** @var array<string, int> 1 = nửa cột, 2 = full width trên md */
    private const FIELD_COL_SPAN = [
        'name' => 1,
        'tagline' => 2,
        'google_site_verification' => 2,
        'about_meta_description' => 2,
        'contact_meta_description' => 2,
        'company_name' => 2,
        'phone' => 1,
        'email' => 1,
        'address' => 2,
        'google_maps_url' => 2,
        'google_maps_embed' => 2,
        'zalo' => 1,
        'facebook' => 1,
        'messenger' => 1,
        'youtube' => 1,
        'tiktok' => 1,
        'hero_desktop' => 1,
        'hero_mobile' => 1,
        'about_main' => 1,
        'about_small' => 1,
    ];

    public function edit(SettingService $settings): View
    {
        $groups = Setting::query()->orderBy('group')->orderBy('id')->get()->groupBy('group');

        foreach (self::GROUP_KEY_ORDER as $group => $keys) {
            if (! $groups->has($group)) {
                continue;
            }

            $groups[$group] = $groups[$group]
                ->sortBy(fn (Setting $item) => array_search($item->key, $keys, true) !== false
                    ? array_search($item->key, $keys, true)
                    : 999)
                ->values();
        }

        $groups = $groups->sortBy(fn ($items, $group) => ($index = array_search($group, self::GROUP_ORDER, true)) !== false
            ? $index
            : 999);

        $groupLabels = self::GROUP_LABELS;
        $fieldColSpan = self::FIELD_COL_SPAN;

        return view('admin.settings.edit', compact('groups', 'groupLabels', 'fieldColSpan'));
    }

    public function update(UpdateSettingRequest $request, SettingService $settings, ImageUploadService $uploader): RedirectResponse
    {
        $items = Setting::query()->get();
        $validated = $request->validated();

        foreach ($items as $item) {
            $key = $item->key;

            if ($item->type === 'image') {
                $existingField = 'existing_' . $key;

                try {
                    if ($request->hasFile($key)) {
                        $uploader->delete($item->value);
                        $folder = 'uploads/settings/' . date('Y/m');
                        $result = $this->uploadSettingImage($uploader, $request->file($key), $folder, $key);
                        $settings->set($key, $result['path']);
                    } elseif ($request->filled($existingField)) {
                        resolve_kept_upload_path(
                            $request->input($existingField),
                            $item->value,
                            'uploads/settings',
                            $key,
                        );
                    } elseif ($item->value) {
                        $uploader->delete($item->value);
                        $settings->set($key, '');
                    }
                } catch (RuntimeException $e) {
                    return back()->withErrors([$key => $e->getMessage()])->withInput();
                }

                continue;
            }

            if (! array_key_exists($key, $validated)) {
                continue;
            }

            $value = $validated[$key];

            if ($key === 'google_maps_embed') {
                $value = GoogleMapsEmbedSanitizer::sanitize($value);
            }

            $settings->set($key, $value ?? '');
        }

        return back()->with('success', 'Cập nhật cài đặt thành công.');
    }

    private function uploadSettingImage(ImageUploadService $uploader, UploadedFile $file, string $folder, string $key): array
    {
        $heroMaxWidth = match ($key) {
            'hero_desktop' => (int) config('media.hero_desktop_max_width', 1920),
            'hero_mobile' => (int) config('media.hero_mobile_max_width', 768),
            default => null,
        };

        if ($heroMaxWidth !== null) {
            return $uploader->upload($file, $folder, null, $heroMaxWidth);
        }

        return $uploader->upload($file, $folder);
    }
}
