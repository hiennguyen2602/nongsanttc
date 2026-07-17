<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /** @return array<string, mixed> */
    public function toModelData(): array
    {
        $category = $this->route('category');

        return [
            'name' => $this->input('name'),
            'slug' => generate_unique_slug(
                $this->input('name'),
                'categories',
                $category instanceof Category ? $category->id : null,
            ),
            'sort_order' => max(1, (int) ($this->input('sort_order') ?? 1)),
        ];
    }
}
