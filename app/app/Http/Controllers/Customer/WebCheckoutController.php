<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\Customer\StoreOrderRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebCheckoutController extends MobileCheckoutController
{
    public function show(Request $request): View
    {
        $products = $this->activeProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->cartData($request, $products);
        $checkoutToken = $this->checkoutToken($request);

        return view('customer.web.checkout', compact(
            'products', 'cart', 'cartProducts', 'cartCount', 'cartTotal', 'checkoutToken',
        ));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        return parent::__invoke($request);
    }
}
