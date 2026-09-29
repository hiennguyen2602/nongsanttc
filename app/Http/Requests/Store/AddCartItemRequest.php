<?php

namespace App\Http\Requests\Store;

use App\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $maxQty = CartService::MAX_QUANTITY;

        return [
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:' . $maxQty],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'quantity.max' => 'Số lượng vượt quá giới hạn cho phép.',
        ];
    }
}
