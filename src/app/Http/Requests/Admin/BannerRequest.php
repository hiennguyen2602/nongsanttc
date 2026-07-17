<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\HasImageUploadMessages;
use App\Models\Banner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

abstract class BannerRequest extends FormRequest
{
    use HasImageUploadMessages;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:500'],
            'position' => ['required', 'string', 'in:home_cta'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => image_upload_file_rules(['nullable']),
            'image_mobile' => image_upload_file_rules(['nullable']),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return array_merge(
            $this->imageUploadMessages('image', 'image_mobile'),
            [
                'position.in' => 'Vị trí banner không hợp lệ.',
            ],
        );
    }

    /** @return array<string, mixed> */
    public function toModelData(): array
    {
        return [
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle'),
            'link' => $this->input('link'),
            'position' => $this->input('position'),
            'sort_order' => (int) ($this->input('sort_order') ?? 0),
            'is_active' => $this->boolean('is_active'),
        ];
    }

    abstract protected function validateDesktopImage(Validator $validator): void;
}
