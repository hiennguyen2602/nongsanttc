<?php

namespace App\Http\Requests\Admin;

use App\Models\Banner;
use Illuminate\Validation\Validator;

class UpdateBannerRequest extends BannerRequest
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateDesktopImage($validator);
        });
    }

    protected function validateDesktopImage(Validator $validator): void
    {
        if ($this->hasFile('image') || filled($this->input('existing_image'))) {
            return;
        }

        $validator->errors()->add('image', 'Vui lòng chọn ảnh desktop.');
    }

    public function banner(): Banner
    {
        /** @var Banner $banner */
        $banner = $this->route('banner');

        return $banner;
    }
}
