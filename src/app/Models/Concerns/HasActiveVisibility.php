<?php

namespace App\Models\Concerns;

trait HasActiveVisibility
{
    /** @return array{active: string, inactive: string} */
    public static function visibilityLabels(): array
    {
        return config('labels.visibility');
    }

    public function visibilityLabel(): string
    {
        $labels = static::visibilityLabels();

        return $this->is_active ? $labels['active'] : $labels['inactive'];
    }

    public function visibilityBadgeClass(): string
    {
        return $this->is_active ? 'badge-success' : 'badge-secondary';
    }
}
