<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CartService $cart,
    ) {}

    public function createFromCart(array $customer, ?string $promoCode = null): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new \RuntimeException('Giỏ hàng trống.');
        }

        $subtotal = $this->cart->subtotal();
        $shipping = $this->cart->shippingFee();
        $discount = $this->cart->discount($promoCode);
        $total = $this->cart->total($promoCode);

        $order = Order::query()->create([
            'order_code' => $this->generateOrderCode(),
            'customer_name' => $customer['name'],
            'customer_phone' => $customer['phone'],
            'customer_email' => $customer['email'] ?? null,
            'customer_address' => $customer['address'],
            'note' => $customer['note'] ?? null,
            'subtotal' => $subtotal,
            'shipping_fee' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'promo_code' => $promoCode ? strtoupper(trim($promoCode)) : null,
            'payment_method' => 'cod',
            'status' => Order::STATUS_PENDING,
        ]);

        foreach ($items as $item) {
            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['variant_id'],
                'product_name' => $item['name'],
                'variant_label' => $item['variant_label'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['unit_price'] * $item['quantity'],
            ]);
        }

        $this->cart->clear();

        return $order->load('items');
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'TTC' . strtoupper(Str::random(8));
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }
}
