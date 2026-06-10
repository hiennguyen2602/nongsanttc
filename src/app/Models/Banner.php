<?php

namespace App\Models;

use App\Models\Concerns\HasActiveVisibility;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasActiveVisibility;

    protected $fillable = [
        'title', 'subtitle', 'image', 'image_mobile', 'link', 'position', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return array<string, string> */
    public static function positionLabels(): array
    {
        return config('labels.banner_position');
    }

    public function positionLabel(): string
    {
        return self::positionLabels()[$this->position] ?? $this->position;
    }
}
