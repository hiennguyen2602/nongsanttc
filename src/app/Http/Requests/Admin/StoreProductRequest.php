<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Validator;

class StoreProductRequest extends ProductFormRequest
{
    protected function validateHasImage(Validator $validator): void
    {
        foreach ((array) $this->input('existing_images', []) as $path) {
            if (filled($path)) {
                $validator->errors()->add('images', 'Ảnh không hợp lệ.');

                return;
            }
        }

        $hasNew = collect((array) $this->file('images', []))->filter()->isNotEmpty();

        if (! $hasNew) {
            $validator->errors()->add('images', 'Vui lòng chọn ít nhất một ảnh sản phẩm.');
        }
    }
}
