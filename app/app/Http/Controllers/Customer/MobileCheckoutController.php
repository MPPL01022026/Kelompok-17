<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\Customer\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileCheckoutController extends MobileStorefrontController
{
    public function __invoke(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $expectedToken = $request->session()->get('customer_checkout_token');

        if (! is_string($expectedToken) || ! hash_equals($expectedToken, $data['checkout_token'])) {
            throw ValidationException::withMessages([
                'checkout_token' => 'Sesi checkout telah berubah. Muat ulang halaman lalu coba lagi.',
            ]);
        }

        $cart = collect($data['items'])
            ->mapWithKeys(fn (mixed $quantity, string|int $productId): array => [
                (string) $productId => (int) $quantity,
            ])
            ->sortKeys()
            ->all();
        $tokenHash = hash('sha256', $data['checkout_token']);
        $lockKey = "checkout:{$tokenHash}";
        $usedTokenKey = "checkout-used:{$tokenHash}";

        try {
            $orderData = Cache::lock($lockKey, 15)->block(5, function () use ($cart, $data, $request, $usedTokenKey): array {
                if (Cache::has($usedTokenKey)) {
                    throw ValidationException::withMessages([
                        'checkout_token' => 'Pesanan ini sudah diproses. Periksa riwayat pesananmu.',
                    ]);
                }

                $orderData = DB::transaction(function () use ($cart, $data, $request): array {
                    $products = Product::query()
                        ->whereIn('id', array_keys($cart))
                        ->where('active', true)
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    if ($products->count() !== count($cart)) {
                        throw ValidationException::withMessages(['cart' => 'Salah satu produk sudah tidak tersedia.']);
                    }

                    $items = [];
                    $total = 0;

                    foreach ($cart as $productId => $quantity) {
                        $product = $products->get($productId);

                        if ($product === null || $product->stock < $quantity) {
                            $availableStock = $product?->stock ?? 0;
                            $productName = $product?->name ?? 'Produk';

                            throw ValidationException::withMessages([
                                'cart' => "Stok {$productName} berubah dan tersisa {$availableStock} porsi. Periksa kembali keranjang.",
                            ]);
                        }

                        $items[] = [
                            'product_id' => $productId,
                            'name' => $product->name,
                            'quantity' => $quantity,
                            'price' => $product->price,
                        ];
                        $total += $product->price * $quantity;
                    }

                    foreach ($cart as $productId => $quantity) {
                        $product = $products->get($productId);
                        $product->stock -= $quantity;
                        $product->save();
                    }

                    $orderData = [
                        'id' => $this->generateOrderCode($request->session()->get('mobile_order_codes', [])),
                        'customer_name' => $data['customer_name'],
                        'phone' => $data['phone'],
                        'items' => json_encode($items, JSON_THROW_ON_ERROR),
                        'total' => $total,
                        'note' => $data['note'] ?? '',
                        'payment_method' => in_array($data['payment_method'], ['cod', 'tunai'], true) ? 'cod' : 'whatsapp',
                        'status' => 'pending',
                        'stock_reserved' => true,
                    ];

                    Order::query()->create($orderData);

                    return $orderData;
                }, 3);

                Cache::put($usedTokenKey, $orderData['id'], now()->addDay());

                return $orderData;
            });
        } catch (LockTimeoutException) {
            throw ValidationException::withMessages([
                'checkout_token' => 'Pesanan sedang diproses. Tunggu sebentar lalu periksa riwayat.',
            ]);
        } catch (QueryException $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'cart' => 'Stok belum dapat direservasi. Silakan coba lagi beberapa saat.',
            ]);
        }

        $this->rememberOrder($request, $orderData);
        $request->session()->forget(['cart', 'customer_checkout_token']);

        $paymentRoute = $request->routeIs('web.checkout.store') ? 'web.payment' : 'mobile.payment';

        return redirect()->route($paymentRoute, $orderData['id'])->with('clear_mobile_cart', true);
    }

    /**
     * @param  array<int, string>  $knownOrderCodes
     */
    private function generateOrderCode(array $knownOrderCodes): string
    {
        do {
            $code = Order::generateCode();
        } while (in_array($code, $knownOrderCodes, true));

        return $code;
    }
}
