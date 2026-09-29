<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'min_order' => ['required', 'integer', 'min:0'],
            'discount_amount' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, mixed> */
    public function toModelData(): array
    {
        return [
            'code' => strtoupper(trim($this->input('code'))),
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'min_order' => (int) $this->input('min_order'),
            'discount_amount' => (int) $this->input('discount_amount'),
            'is_active' => $this->boolean('is_active', true),
        ];
    }
}
