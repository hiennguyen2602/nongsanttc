<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Validator;

class StoreBannerRequest extends BannerRequest
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateDesktopImage($validator);
        });
    }

    protected function validateDesktopImage(Validator $validator): void
    {
        if (! $this->hasFile('image')) {
            $validator->errors()->add('image', 'Vui lòng chọn ảnh desktop.');
        }
    }
}
