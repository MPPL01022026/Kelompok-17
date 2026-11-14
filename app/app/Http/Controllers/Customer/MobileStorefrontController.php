<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class MobileStorefrontController extends Controller
{
    protected function activeProducts(): Collection
    {
        Product::seedDefaults();

        try {
            $products = Product::query()->where('active', true)->get();

            if ($products->isNotEmpty()) {
                $priority = collect(Product::DEFAULT_PRODUCTS)->pluck('id')->flip();
                $defaultImages = collect(Product::DEFAULT_PRODUCTS)->pluck('image_url', 'id');

                return $products
                    ->each(function (Product $product) use ($defaultImages): void {
                        if (blank($product->image_url) || str_ends_with($product->image_url, '.svg')) {
                            $categoryFallback = str_contains(mb_strtolower((string) $product->category), 'camilan')
                                ? '/img/cat-bakso.png'
                                : '/img/cat-dada.png';
                            $product->image_url = $defaultImages->get($product->id, $categoryFallback);
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

    protected function checkoutToken(Request $request): string
    {
        $token = $request->session()->get('customer_checkout_token');

        if (! is_string($token) || ! Str::isUuid($token)) {
            $token = (string) Str::uuid();
            $request->session()->put('customer_checkout_token', $token);
        }

        return $token;
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

    protected function normalizePhoneDigits(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return $digits;
    }

    protected function handleTrackOrderQuery(Request $request): void
    {
        if (! $request->filled('code') && ! $request->filled('phone')) {
            return;
        }

        $code = strtoupper(trim((string) $request->query('code', '')));
        $phoneDigits = $this->normalizePhoneDigits((string) $request->query('phone', ''));

        if ($code === '' || $phoneDigits === '') {
            $request->session()->now('error', 'Masukkan kode pesanan dan nomor WhatsApp untuk melacak pesanan.');

            return;
        }

        try {
            $order = Order::query()->find($code);
        } catch (QueryException) {
            $order = null;
        }

        if ($order === null) {
            $sessionOrder = collect($request->session()->get('mobile_orders_data', []))->get($code);
            $order = is_array($sessionOrder) ? new Order($sessionOrder) : null;
        }

        if ($order === null || ! hash_equals($this->normalizePhoneDigits((string) $order->phone), $phoneDigits)) {
            $request->session()->now('error', 'Kode pesanan dan nomor WhatsApp tidak cocok atau tidak ditemukan.');

            return;
        }

        $this->rememberOrder($request, $order->toArray());
        $request->session()->now('status', "Pesanan {$order->id} ditemukan dan ditampilkan di riwayat.");
    }
}
