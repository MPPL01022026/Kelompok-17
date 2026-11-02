<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WebHistoryController extends MobileStorefrontController
{
    public function __invoke(Request $request): View
    {
        [, , $cartCount] = $this->cartData($request);
        $knownOrderCodes = array_slice($request->session()->get('mobile_order_codes', []), 0, 30);
        $sessionOrders = collect($request->session()->get('mobile_orders_data', []));
        $databaseOrders = new Collection;

        if ($knownOrderCodes !== []) {
            try {
                $databaseOrders = Order::query()->whereIn('id', $knownOrderCodes)->get()->keyBy('id');
            } catch (QueryException) {
                // Session orders remain accessible when the database is unavailable.
            }
        }

        $orders = collect($knownOrderCodes)
            ->map(function (string $code) use ($databaseOrders, $sessionOrders): ?Order {
                $order = $databaseOrders->get($code);

                if ($order !== null) {
                    return $order;
                }

                $sessionOrder = $sessionOrders->get($code);

                return is_array($sessionOrder) ? new Order($sessionOrder) : null;
            })
            ->filter()
            ->values();

        return view('customer.web.history', compact('orders', 'cartCount'));
    }
}
