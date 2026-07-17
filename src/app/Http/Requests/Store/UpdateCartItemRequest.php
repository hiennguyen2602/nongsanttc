<?php

namespace App\Http\Requests\Store;

use App\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
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
            'key' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:0', 'max:' . $maxQty],
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
