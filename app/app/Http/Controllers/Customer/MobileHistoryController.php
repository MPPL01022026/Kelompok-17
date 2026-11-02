<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MobileHistoryController extends MobileStorefrontController
{
    public function __invoke(Request $request): View
    {
        $products = $this->activeProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->cartData($request, $products);
        $knownOrderCodes = array_slice($request->session()->get('mobile_order_codes', []), 0, 30);
        $sessionOrders = collect($request->session()->get('mobile_orders_data', []));
        $databaseOrders = new Collection;

        if ($knownOrderCodes !== []) {
            try {
                $databaseOrders = Order::query()
                    ->whereIn('id', $knownOrderCodes)
                    ->get()
                    ->keyBy('id');
            } catch (QueryException) {
                // Session-backed orders are still available when the database is offline.
            }
        }

        $orders = collect($knownOrderCodes)
            ->map(function (string $code) use ($databaseOrders, $sessionOrders): ?Order {
                $databaseOrder = $databaseOrders->get($code);

                if ($databaseOrder !== null) {
                    return $databaseOrder;
                }

                $sessionOrder = $sessionOrders->get($code);

                return is_array($sessionOrder) ? new Order($sessionOrder) : null;
            })
            ->filter()
            ->values();

        return view('customer.mobile.history', compact(
            'orders', 'cart', 'cartProducts', 'cartCount', 'cartTotal',
        ));
    }
}
