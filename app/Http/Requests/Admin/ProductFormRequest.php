<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\HasImageUploadMessages;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

abstract class ProductFormRequest extends FormRequest
{
    use HasImageUploadMessages;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sale_price' => blank($this->input('sale_price')) ? null : $this->input('sale_price'),
            'stock' => blank($this->input('stock')) ? null : $this->input('stock'),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'price' => ['required', 'integer', 'min:1'],
            'sale_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => image_upload_file_rules(['nullable']),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return $this->imageUploadMessages('images.*');
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateVariants($validator);
            $this->validateHasImage($validator);
        });
    }

    protected function validateVariants(Validator $validator): void
    {
        foreach ((array) $this->input('variants', []) as $index => $variant) {
            $flavor = trim((string) ($variant['flavor'] ?? ''));
            $size = trim((string) ($variant['size'] ?? ''));
            $price = trim((string) ($variant['price'] ?? ''));

            if (($flavor !== '' || $size !== '') && $price === '') {
                $validator->errors()->add("variants.{$index}.price", 'Vui lòng nhập giá cho biến thể.');
            }
        }
    }

    abstract protected function validateHasImage(Validator $validator): void;

    /** @return array<string, mixed> */
    public function toModelData(): array
    {
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : null;

        $sku = trim((string) ($this->input('sku') ?? ''));

        return [
            'category_id' => $this->input('category_id'),
            'name' => $this->input('name'),
            'sku' => $sku !== '' ? $sku : generate_unique_sku(),
            'description' => $this->input('description'),
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'price' => (int) $this->input('price'),
            'sale_price' => $this->input('sale_price') !== null ? (int) $this->input('sale_price') : null,
            'stock' => $this->input('stock') !== null ? (int) $this->input('stock') : null,
            'slug' => generate_unique_slug($this->input('name'), 'products', $productId),
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active', true),
        ];
    }

    /** @return list<string> */
    protected function keptProductImages(Product $product): array
    {
        $submitted = (array) $this->input('existing_images', []);
        $allowed = array_filter(array_merge([$product->image], (array) $product->gallery));

        return kept_upload_paths($submitted, $allowed, 'uploads/products', 'images');
    }

    protected function mergeKeptImageErrors(Validator $validator, callable $callback): void
    {
        try {
            $callback();
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $validator->errors()->add($field, $message);
                }
            }
        }
    }
}
