<?php

namespace App\Http\Controllers\Customer;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebCartController extends MobileCartController
{
    public function show(Request $request): View
    {
        $products = $this->activeProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->cartData($request, $products);
        $settings = Settings::getMap();

        return view('customer.web.cart', compact(
            'products', 'cart', 'cartProducts', 'cartCount', 'cartTotal', 'settings',
        ));
    }
}
