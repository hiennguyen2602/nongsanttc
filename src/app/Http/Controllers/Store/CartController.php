<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $variantId = ! empty($data['variant_id']) ? (int) $data['variant_id'] : null;

        $cart->add(
            (int) $data['product_id'],
            (int) ($data['quantity'] ?? 1),
            $variantId,
        );

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, CartService $cart): RedirectResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $cart->update($data['key'], (int) $data['quantity']);

        return back()->with('success', 'Cập nhật giỏ hàng thành công.');
    }

    public function remove(string $key, CartService $cart): RedirectResponse
    {
        $cart->remove($key);

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
