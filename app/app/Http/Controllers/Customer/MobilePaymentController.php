<?php

namespace App\Http\Controllers\Customer;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobilePaymentController extends MobileStorefrontController
{
    public function show(Request $request, string $code): View
    {
        $order = $this->ownedOrder($request, $code);
        $settings = Settings::getMap();
        $channel = $request->string('channel', 'bank')->toString();

        abort_unless(in_array($channel, ['bank', 'qris'], true), 404);

        return view('customer.mobile.payment', compact('order', 'settings', 'channel'));
    }
}
