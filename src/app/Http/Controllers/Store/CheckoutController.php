<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Promotion;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function index(CartService $cart, Request $request): View|RedirectResponse
    {
        if ($cart->items()->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Giỏ hàng trống.']);
        }

        try {
            $items = $cart->resolveItems();
        } catch (RuntimeException $e) {
            return redirect()->route('cart.index')->withErrors(['cart' => $e->getMessage()]);
        }

        $promoCode = $request->session()->get('promo_code');
        $promo = $promoCode
            ? Promotion::query()->where('code', $promoCode)->where('is_active', true)->first()
            : null;

        $subtotal = $cart->subtotal($items);

        return view('store.checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shippingFee' => $cart->shippingFee($subtotal),
            'discount' => $cart->discount($promoCode, $subtotal),
            'total' => $cart->total($promoCode, $items),
            'promoCode' => $promoCode,
            'promo' => $promo,
        ]);
    }

    public function applyPromo(Request $request, CartService $cart): RedirectResponse
    {
        $request->validate(['promo_code' => ['required', 'string', 'max:50']]);
        $code = strtoupper(trim($request->promo_code));

        $promo = Promotion::query()->where('code', $code)->where('is_active', true)->first();

        if (! $promo) {
            return back()->withErrors(['promo_code' => 'Mã khuyến mãi không hợp lệ.']);
        }

        try {
            $items = $cart->resolveItems();
            $subtotal = $cart->subtotal($items);
        } catch (RuntimeException $e) {
            return redirect()->route('cart.index')->withErrors(['cart' => $e->getMessage()]);
        }

        if ($subtotal < $promo->min_order) {
            return back()->withErrors(['promo_code' => 'Đơn hàng chưa đủ điều kiện áp dụng mã.']);
        }

        $request->session()->put('promo_code', $code);

        return back()->with('success', 'Áp dụng mã khuyến mãi thành công.');
    }

    public function store(Request $request, OrderService $orders, CartService $cart): RedirectResponse
    {
        if ($cart->items()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $customer = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', function (string $attribute, mixed $value, \Closure $fail) {
                if (! Customer::isValidVietnamesePhone((string) $value)) {
                    $fail('Số điện thoại phải có 10 chữ số (Việt Nam).');
                }
            }],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer['phone'] = Customer::normalizePhone($customer['phone']);

        $promoCode = $request->session()->get('promo_code');

        try {
            $order = $orders->createFromCart($customer, $promoCode);
        } catch (RuntimeException $e) {
            return redirect()->route('cart.index')->withErrors(['cart' => $e->getMessage()]);
        }

        $request->session()->forget('promo_code');
        $request->session()->put('checkout_success_token', $order->public_token);

        return redirect()->route('checkout.success', ['token' => $order->public_token])
            ->with('success', 'Đặt hàng thành công.');
    }

    public function success(Request $request, string $token): View|RedirectResponse
    {
        if ($request->session()->pull('checkout_success_token') !== $token) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        $order = Order::query()->where('public_token', $token)->firstOrFail();
        $order->load('items');

        return view('store.checkout.success', compact('order'));
    }
}
