<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'flavor', 'size', 'price', 'stock'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function label(): string
    {
        return collect([$this->flavor, $this->size])->filter()->implode(' — ');
    }

    public function formattedPrice(): string
    {
        return $this->price !== null ? number_format($this->price, 0, ',', '.') . 'đ' : '—';
    }

    public function formattedStock(): string
    {
        return $this->stock !== null ? number_format($this->stock, 0, ',', '.') : '—';
    }
}
