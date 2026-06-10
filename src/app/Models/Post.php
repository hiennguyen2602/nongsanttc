<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'image', 'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /** @return array{published: string, draft: string} */
    public static function publishStatusLabels(): array
    {
        return config('labels.publish');
    }

    public function publishStatusLabel(): string
    {
        $labels = self::publishStatusLabels();

        return $this->is_published ? $labels['published'] : $labels['draft'];
    }

    public function publishStatusBadgeClass(): string
    {
        return $this->is_published ? 'badge-success' : 'badge-secondary';
    }
}
