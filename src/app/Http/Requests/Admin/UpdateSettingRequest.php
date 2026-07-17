<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\HasImageUploadMessages;
use App\Models\Setting;
use App\Rules\ValidGoogleMapsEmbed;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    use HasImageUploadMessages;

    /** @var list<string> */
    private const URL_KEYS = [
        'google_maps_url',
        'zalo',
        'facebook',
        'messenger',
        'youtube',
        'tiktok',
    ];

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [];

        foreach (Setting::query()->get() as $item) {
            $key = $item->key;

            if ($item->type === 'image') {
                $rules[$key] = image_upload_file_rules(['nullable']);
                continue;
            }

            if ($key === 'google_maps_embed') {
                $rules[$key] = ['nullable', 'string', 'max:10000', new ValidGoogleMapsEmbed];
                continue;
            }

            if (in_array($key, self::URL_KEYS, true)) {
                $rules[$key] = ['nullable', 'string', 'max:500', 'url'];
                continue;
            }

            if ($key === 'email') {
                $rules[$key] = ['nullable', 'email', 'max:255'];
                continue;
            }

            if ($key === 'phone') {
                $rules[$key] = ['nullable', 'string', 'max:20'];
                continue;
            }

            if ($key === 'google_site_verification') {
                $rules[$key] = ['nullable', 'string', 'max:255'];
                continue;
            }

            $rules[$key] = match ($item->type) {
                'textarea' => ['nullable', 'string', 'max:5000'],
                default => ['nullable', 'string', 'max:255'],
            };
        }

        return $rules;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return array_merge(
            $this->imageUploadMessages('*'),
            [
                '*.url' => 'URL không hợp lệ.',
                '*.email' => 'Email không hợp lệ.',
                '*.max' => 'Giá trị vượt quá giới hạn cho phép.',
            ],
        );
    }
}
