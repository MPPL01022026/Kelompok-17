<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class MobileStorefrontController extends Controller
{
    protected function activeProducts(): Collection
    {
        try {
            $products = Product::query()->where('active', true)->get();

            if ($products->isNotEmpty()) {
                $priority = collect(Product::DEFAULT_PRODUCTS)->pluck('id')->flip();
                $defaultImages = collect(Product::DEFAULT_PRODUCTS)->pluck('image_url', 'id');

                return $products
                    ->each(function (Product $product) use ($defaultImages): void {
                        if (blank($product->image_url) || str_ends_with($product->image_url, '.svg')) {
                            $product->image_url = $defaultImages->get($product->id, $product->image_url);
                        }
                    })
                    ->sortBy(fn (Product $product): int => $priority->get($product->id, PHP_INT_MAX))
                    ->values();
            }
        } catch (QueryException) {
            // The static catalog keeps the mobile storefront usable before setup is complete.
        }

        return collect(Product::DEFAULT_PRODUCTS)
            ->map(fn (array $product): Product => new Product($product + ['active' => true]));
    }

    /**
     * @return array{0: array<string, int>, 1: Collection<string, Product>, 2: int, 3: int}
     */
    protected function cartData(Request $request, ?Collection $products = null): array
    {
        $catalog = $products ?? $this->activeProducts();
        $cart = collect($request->session()->get('cart', []))
            ->map(fn (mixed $quantity): int => max(0, (int) $quantity))
            ->filter()
            ->all();
        $cartProducts = $catalog->whereIn('id', array_keys($cart))->keyBy('id');
        $cartCount = 0;
        $cartTotal = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $cartProducts->get($productId);

            if ($product === null) {
                continue;
            }

            $cartCount += $quantity;
            $cartTotal += $product->price * $quantity;
        }

        return [$cart, $cartProducts, $cartCount, $cartTotal];
    }

    protected function ownedOrder(Request $request, string $code): Order
    {
        $sessionOrders = $request->session()->get('mobile_orders_data', []);
        $knownOrderCodes = $request->session()->get('mobile_order_codes', []);
        abort_unless(in_array($code, $knownOrderCodes, true) || isset($sessionOrders[$code]), 404);

        try {
            $order = Order::query()->find($code);

            if ($order !== null) {
                return $order;
            }
        } catch (QueryException) {
            // Fall back to the session copy when the database is unavailable.
        }

        if (isset($sessionOrders[$code])) {
            return new Order($sessionOrders[$code]);
        }

        abort(404);
    }

    /**
     * @param  array<string, mixed>  $orderData
     */
    protected function rememberOrder(Request $request, array $orderData): void
    {
        $code = (string) $orderData['id'];
        $sessionOrders = $request->session()->get('mobile_orders_data', []);
        $sessionOrders[$code] = $orderData;

        $knownOrderCodes = $request->session()->get('mobile_order_codes', []);
        array_unshift($knownOrderCodes, $code);

        $request->session()->put('mobile_orders_data', $sessionOrders);
        $request->session()->put('mobile_order_codes', array_values(array_unique($knownOrderCodes)));
    }
}
