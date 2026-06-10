<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Support\Collection;
use RuntimeException;

class CartService
{
    private const SESSION_KEY = 'cart';

    /** Giới hạn cột unsignedInteger trong DB (MySQL). */
    public const MAX_QUANTITY = 4_294_967_295;

    public function items(): Collection
    {
        $raw = collect(session(self::SESSION_KEY, []));

        if ($raw->isEmpty()) {
            return collect();
        }

        $missingSlugIds = $raw
            ->filter(fn (array $item) => empty($item['slug']) && ! empty($item['product_id']))
            ->pluck('product_id')
            ->unique()
            ->values();

        $slugsByProductId = $missingSlugIds->isNotEmpty()
            ? Product::query()->whereIn('id', $missingSlugIds)->pluck('slug', 'id')
            : collect();

        return $raw->map(function (array $item) use ($slugsByProductId) {
            if (empty($item['slug']) && ! empty($item['product_id'])) {
                $item['slug'] = $slugsByProductId->get((int) $item['product_id']);
            }

            return $item;
        });
    }

    /**
     * Lấy giỏ hàng với giá và thông tin SP mới nhất từ DB (dùng khi checkout).
     *
     * @throws RuntimeException
     */
    public function resolveItems(): Collection
    {
        $raw = collect(session(self::SESSION_KEY, []));

        if ($raw->isEmpty()) {
            return collect();
        }

        $productIds = $raw->pluck('product_id')->unique()->filter()->values();
        $variantIds = $raw->pluck('variant_id')->unique()->filter()->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $variants = $variantIds->isNotEmpty()
            ? ProductVariant::query()->whereIn('id', $variantIds)->get()->keyBy('id')
            : collect();

        return $raw->map(function (array $item) use ($products, $variants) {
            $productId = (int) ($item['product_id'] ?? 0);
            $product = $products->get($productId);

            if (! $product) {
                throw new RuntimeException('Một sản phẩm trong giỏ không còn khả dụng. Vui lòng cập nhật giỏ hàng.');
            }

            $variantId = ! empty($item['variant_id']) ? (int) $item['variant_id'] : null;
            $variant = $variantId ? $variants->get($variantId) : null;

            if ($variantId && (! $variant || $variant->product_id !== $product->id)) {
                throw new RuntimeException('Biến thể sản phẩm không còn hợp lệ. Vui lòng cập nhật giỏ hàng.');
            }

            $quantity = (int) ($item['quantity'] ?? 1);
            $unitPrice = (int) ($variant?->price ?? $product->displayPrice());

            $this->assertValidQuantity($quantity, $unitPrice);

            return [
                'key' => $item['key'] ?? $this->itemKey($productId, $variantId),
                'product_id' => $product->id,
                'slug' => $product->slug,
                'variant_id' => $variant?->id,
                'name' => $product->name,
                'variant_label' => $variant?->label(),
                'image' => $product->image,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
            ];
        })->values();
    }

    public function count(): int
    {
        return (int) collect(session(self::SESSION_KEY, []))->sum('quantity');
    }

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): void
    {
        $product = Product::query()->where('is_active', true)->findOrFail($productId);
        $variant = $variantId
            ? ProductVariant::query()->where('product_id', $productId)->findOrFail($variantId)
            : null;

        $key = $this->itemKey($productId, $variantId);
        $cart = session(self::SESSION_KEY, []);
        $unitPrice = (int) ($variant?->price ?? $product->displayPrice());
        $newQuantity = isset($cart[$key])
            ? (int) $cart[$key]['quantity'] + $quantity
            : max(1, $quantity);

        $this->assertValidQuantity($newQuantity, $unitPrice);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $newQuantity;
            $cart[$key]['unit_price'] = $unitPrice;
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
                'quantity' => $newQuantity,
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
            session([self::SESSION_KEY => $cart]);

            return;
        }

        $unitPrice = (int) ($cart[$key]['unit_price'] ?? 0);
        $this->assertValidQuantity($quantity, $unitPrice);

        $cart[$key]['quantity'] = $quantity;
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

    public function subtotal(?Collection $items = null): int
    {
        $items ??= $this->items();

        return (int) $items->sum(fn ($item) => (int) $item['unit_price'] * (int) $item['quantity']);
    }

    public function shippingFee(?int $subtotal = null): int
    {
        $subtotal ??= $this->subtotal();

        return $subtotal >= 350000 ? 0 : 30000;
    }

    public function discount(?string $promoCode = null, ?int $subtotal = null): int
    {
        if (! $promoCode) {
            return 0;
        }

        $subtotal ??= $this->subtotal();

        $promo = Promotion::query()
            ->where('code', strtoupper(trim($promoCode)))
            ->where('is_active', true)
            ->first();

        if (! $promo || $subtotal < $promo->min_order) {
            return 0;
        }

        return min($promo->discount_amount, $subtotal);
    }

    public function total(?string $promoCode = null, ?Collection $items = null): int
    {
        $items ??= $this->items();
        $subtotal = $this->subtotal($items);

        return max(0, $subtotal + $this->shippingFee($subtotal) - $this->discount($promoCode, $subtotal));
    }

    public function assertValidQuantity(int $quantity, int $unitPrice = 0): void
    {
        if ($quantity < 1) {
            throw new RuntimeException('Số lượng phải lớn hơn 0.');
        }

        if ($quantity > self::MAX_QUANTITY) {
            throw new RuntimeException('Số lượng vượt quá giới hạn cho phép.');
        }

        if ($unitPrice > 0 && $quantity > intdiv(self::MAX_QUANTITY, $unitPrice)) {
            throw new RuntimeException('Số lượng quá lớn so với đơn giá sản phẩm.');
        }
    }

    private function itemKey(int $productId, ?int $variantId): string
    {
        return $productId . ':' . ($variantId ?? 'base');
    }
}
