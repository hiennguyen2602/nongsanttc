<?php

namespace App\Services;

use App\Mail\NewOrderMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CustomerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

class OrderService
{
    public function __construct(
        private CartService $cart,
        private CustomerService $customers,
    ) {}

    public function createFromCart(array $customer, ?string $promoCode = null): Order
    {
        return DB::transaction(function () use ($customer, $promoCode) {
            $items = $this->cart->resolveItems();

            if ($items->isEmpty()) {
                throw new RuntimeException('Giỏ hàng trống.');
            }

            $subtotal = $this->cart->subtotal($items);
            $shipping = $this->cart->shippingFee($subtotal);
            $discount = $this->cart->discount($promoCode, $subtotal);
            $total = max(0, $subtotal + $shipping - $discount);

            $phone = Customer::normalizePhone($customer['phone']);
            $record = $this->customers->resolveFromCheckout([
                ...$customer,
                'phone' => $phone,
            ]);

            $order = Order::query()->create([
                'order_code' => $this->generateOrderCode(),
                'public_token' => $this->generatePublicToken(),
                'customer_id' => $record->id,
                'customer_name' => $customer['name'],
                'customer_phone' => $phone,
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

            $this->createOrderItems($order, $items);

            $this->cart->clear();

            $order->load('items');
            $this->notifyStoreOfNewOrder($order);

            return $order;
        });
    }

    private function createOrderItems(Order $order, Collection $items): void
    {
        foreach ($items as $item) {
            $variantId = $item['variant_id'] ?? null;
            $unitPrice = (int) $item['unit_price'];
            $quantity = (int) $item['quantity'];
            $lineTotal = $unitPrice * $quantity;

            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $variantId,
                'product_name' => $item['name'],
                'variant_label' => $item['variant_label'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ]);
        }
    }

    private function notifyStoreOfNewOrder(Order $order): void
    {
        $email = store_setting('email');

        if (! filled($email)) {
            return;
        }

        try {
            Mail::to($email)->send(new NewOrderMail($order));
        } catch (\Throwable $e) {
            Log::error('Failed to send new order notification.', [
                'order_id' => $order->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'NS' . strtoupper(Str::random(8));
        } while (Order::query()->where('order_code', $code)->exists());

        return $code;
    }

    private function generatePublicToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (Order::query()->where('public_token', $token)->exists());

        return $token;
    }
}
