<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function items(): Collection
    {
        return collect(session(self::SESSION_KEY, []))->map(function (array $item) {
            if (empty($item['slug']) && ! empty($item['product_id'])) {
                $item['slug'] = Product::query()->whereKey($item['product_id'])->value('slug');
            }

            return $item;
        });
    }

    public function count(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): void
    {
        $product = Product::query()->where('is_active', true)->findOrFail($productId);
        $variant = $variantId
            ? ProductVariant::query()->where('product_id', $productId)->findOrFail($variantId)
            : null;

        $key = $this->itemKey($productId, $variantId);
        $cart = session(self::SESSION_KEY, []);
        $unitPrice = $variant?->price ?? $product->displayPrice();

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'key' => $key,
                'product_id' => $product->id,
                'slug' => $product->slug,
                'variant_id' => $variant?->id,
                'name' => $product->name,
                'variant_label' => $variant?->label(),
                'image' => $product->image,
                'unit_price' => $unitPrice,
                'quantity' => max(1, $quantity),
            ];
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function update(string $key, int $quantity): void
    {
        $cart = session(self::SESSION_KEY, []);

        if (! isset($cart[$key])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $quantity;
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function remove(string $key): void
    {
        $cart = session(self::SESSION_KEY, []);
        unset($cart[$key]);
        session([self::SESSION_KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
    }

    public function shippingFee(): int
    {
        return $this->subtotal() >= 350000 ? 0 : 30000;
    }

    public function discount(?string $promoCode = null): int
    {
        if (! $promoCode) {
            return 0;
        }

        $promo = Promotion::query()
            ->where('code', strtoupper(trim($promoCode)))
            ->where('is_active', true)
            ->first();

        if (! $promo || $this->subtotal() < $promo->min_order) {
            return 0;
        }

        return min($promo->discount_amount, $this->subtotal());
    }

    public function total(?string $promoCode = null): int
    {
        return max(0, $this->subtotal() + $this->shippingFee() - $this->discount($promoCode));
    }

    private function itemKey(int $productId, ?int $variantId): string
    {
        return $productId . ':' . ($variantId ?? 'base');
    }
}
