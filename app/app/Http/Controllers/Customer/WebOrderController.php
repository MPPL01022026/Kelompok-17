<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebOrderController extends MobileStorefrontController
{
    public function show(Request $request, string $code): View
    {
        $order = $this->ownedOrder($request, $code);
        [, , $cartCount] = $this->cartData($request);

        return view('customer.web.order', compact('order', 'cartCount'));
    }

    public function reorder(Request $request, string $code): RedirectResponse
    {
        $order = $this->ownedOrder($request, $code);
        $products = $this->activeProducts()->keyBy('id');
        $cart = $request->session()->get('cart', []);
        $added = 0;

        foreach ($order->parsed_items as $item) {
            $productId = $item['product_id'] ?? null;
            $product = $productId === null ? null : $products->get($productId);

            if ($product === null || $product->stock < 1) {
                continue;
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $cart[$productId] = min(((int) ($cart[$productId] ?? 0)) + $quantity, $product->stock);
            $added++;
        }

        if ($added === 0) {
            return back()->withErrors(['cart' => 'Menu pada pesanan ini sedang tidak tersedia.']);
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('web.cart')->with('status', "Pesanan {$order->code} ditambahkan ke keranjang.");
    }
}
