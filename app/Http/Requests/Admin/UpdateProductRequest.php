<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends ProductFormRequest
{
    protected function validateHasImage(Validator $validator): void
    {
        /** @var Product $product */
        $product = $this->route('product');

        $hasNew = collect((array) $this->file('images', []))->filter()->isNotEmpty();
        $hasKept = false;

        $this->mergeKeptImageErrors($validator, function () use ($product, &$hasKept) {
            $hasKept = count($this->keptProductImages($product)) > 0;
        });

        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        if (! $hasNew && ! $hasKept) {
            $validator->errors()->add('images', 'Vui lòng chọn ít nhất một ảnh sản phẩm.');
        }
    }

    public function product(): Product
    {
        /** @var Product $product */
        $product = $this->route('product');

        return $product;
    }
}
