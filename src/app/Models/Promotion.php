<?php

namespace App\Models;

use App\Models\Concerns\HasActiveVisibility;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasActiveVisibility;

    protected $fillable = [
        'code',
        'title',
        'description',
        'min_order',
        'discount_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
