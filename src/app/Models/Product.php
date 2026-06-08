<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'description', 'short_description',
        'price', 'sale_price', 'image', 'gallery', 'is_featured', 'is_active', 'stock',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function displayPrice(): int
    {
        return $this->sale_price ?? $this->price;
    }

    public function formattedPrice(): string
    {
        return number_format($this->displayPrice(), 0, ',', '.') . 'đ';
    }
}
