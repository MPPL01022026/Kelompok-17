<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\Customer\StoreOrderRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebCheckoutController extends MobileCheckoutController
{
    public function show(Request $request): View|RedirectResponse
    {
        $products = $this->activeProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->cartData($request, $products);

        if ($cartProducts->isEmpty()) {
            return redirect()->route('web.cart')->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        return view('customer.web.checkout', compact('cart', 'cartProducts', 'cartCount', 'cartTotal'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        return parent::__invoke($request);
    }
}
