<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        return view('store.cart.index', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'shippingFee' => $cart->shippingFee(),
        ]);
    }

    public function add(Request $request, CartService $cart): RedirectResponse
    {
        $maxQty = CartService::MAX_QUANTITY;

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:' . $maxQty],
        ], [
            'quantity.max' => 'Số lượng vượt quá giới hạn cho phép.',
        ]);

        $variantId = ! empty($data['variant_id']) ? (int) $data['variant_id'] : null;

        try {
            $cart->add(
                (int) $data['product_id'],
                (int) ($data['quantity'] ?? 1),
                $variantId,
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, CartService $cart): RedirectResponse|JsonResponse
    {
        $maxQty = CartService::MAX_QUANTITY;

        $data = $request->validate([
            'key' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:0', 'max:' . $maxQty],
        ], [
            'quantity.max' => 'Số lượng vượt quá giới hạn cho phép.',
        ]);

        try {
            $cart->update($data['key'], (int) $data['quantity']);
        } catch (RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['quantity' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return $this->cartTotalsJson($cart, $data['key']);
        }

        return back()->with('success', 'Cập nhật giỏ hàng thành công.');
    }

    private function cartTotalsJson(CartService $cart, string $key): JsonResponse
    {
        $items = $cart->items();
        $subtotal = $cart->subtotal($items);
        $shippingFee = $cart->shippingFee($subtotal);
        $item = $items->firstWhere('key', $key);
        $quantity = $item ? (int) $item['quantity'] : 0;
        $lineTotal = $item ? (int) $item['unit_price'] * $quantity : 0;

        return response()->json([
            'key' => $key,
            'quantity' => $quantity,
            'line_total' => $lineTotal,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'grand_total' => $subtotal + $shippingFee,
            'cart_count' => $cart->count(),
        ]);
    }

    public function remove(string $key, CartService $cart): RedirectResponse
    {
        $cart->remove($key);

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
