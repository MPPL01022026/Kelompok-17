<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\Customer\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileCheckoutController extends MobileStorefrontController
{
    public function __invoke(StoreOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $cart = collect($data['items'])
            ->mapWithKeys(fn (mixed $quantity, string|int $productId): array => [
                (string) $productId => (int) $quantity,
            ])
            ->all();
        $catalog = $this->activeProducts()->keyBy('id');
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $catalog->get($productId);

            if ($product === null) {
                throw ValidationException::withMessages(['cart' => 'Produk tidak ditemukan.']);
            }

            if ($product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'cart' => "Stok {$product->name} tersisa {$product->stock} porsi.",
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

        $orderCode = $this->generateOrderCode($request->session()->get('mobile_order_codes', []));
        $orderData = [
            'id' => $orderCode,
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'items' => json_encode($items, JSON_THROW_ON_ERROR),
            'total' => $total,
            'note' => $data['note'] ?? '',
            'payment_method' => in_array($data['payment_method'], ['cod', 'tunai'], true) ? 'cod' : 'whatsapp',
            'status' => 'pending',
        ];

        try {
            DB::transaction(function () use ($cart, $orderData): void {
                $products = Product::query()
                    ->whereIn('id', array_keys($cart))
                    ->where('active', true)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->isEmpty()) {
                    return;
                }

                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);

                    if ($product === null || $product->stock < $quantity) {
                        throw ValidationException::withMessages([
                            'cart' => 'Stok berubah. Periksa kembali isi keranjang.',
                        ]);
                    }
                }

                Order::query()->create($orderData);
            });
        } catch (QueryException) {
            // The order remains available in this device session if setup is incomplete.
        }

        $this->rememberOrder($request, $orderData);
        $request->session()->forget('cart');

        $paymentRoute = $request->routeIs('web.checkout.store') ? 'web.payment' : 'mobile.payment';

        return redirect()->route($paymentRoute, $orderCode)->with('clear_mobile_cart', true);
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
