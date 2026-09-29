<?php

namespace App\Models;

use App\Models\Concerns\HasActiveVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasActiveVisibility;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'description', 'meta_title', 'meta_description',
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

    public function displayPrice(): ?int
    {
        if ($this->sale_price !== null) {
            return (int) $this->sale_price;
        }

        return $this->price !== null ? (int) $this->price : null;
    }

    public function formattedPrice(): string
    {
        $price = $this->displayPrice();

        return $price !== null ? number_format($price, 0, ',', '.') . 'đ' : '—';
    }

    public function formattedStock(): string
    {
        return $this->stock !== null ? number_format($this->stock, 0, ',', '.') : '—';
    }
}
