<?php

namespace App\Http\Controllers\Customer;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebPaymentController extends MobileStorefrontController
{
    public function show(Request $request, string $code): View
    {
        $order = $this->ownedOrder($request, $code);
        $settings = Settings::getMap();
        [, , $cartCount] = $this->cartData($request);

        return view('customer.web.payment', compact('order', 'settings', 'cartCount'));
    }
}
