<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class RecentlyViewedService
{
    private const SESSION_KEY = 'viewed_products';

    private const MAX_ITEMS = 8;

    public function track(int $productId): void
    {
        $items = collect(session(self::SESSION_KEY, []))
            ->reject(fn ($id) => (int) $id === $productId)
            ->prepend($productId)
            ->take(self::MAX_ITEMS)
            ->values()
            ->all();

        session([self::SESSION_KEY => $items]);
    }

    public function products(?int $excludeId = null): Collection
    {
        $ids = collect(session(self::SESSION_KEY, []))
            ->when($excludeId, fn ($c) => $c->reject(fn ($id) => (int) $id === $excludeId))
            ->take(4)
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($product) => $ids->search($product->id))
            ->values();
    }
}
