<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\HasImageUploadMessages;
use Illuminate\Foundation\Http\FormRequest;

class UploadEditorImageRequest extends FormRequest
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
            'file' => image_upload_file_rules(['required']),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->imageUploadMessages('file');
    }
}
