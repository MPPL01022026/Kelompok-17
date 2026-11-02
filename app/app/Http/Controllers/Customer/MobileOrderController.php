<?php

namespace App\Http\Controllers\Customer;

use App\Models\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileOrderController extends MobileStorefrontController
{
    public function show(Request $request, string $code): View
    {
        $order = $this->ownedOrder($request, $code);
        $settings = Settings::getMap();
        $channel = $request->string('channel', 'bank')->toString();

        abort_unless(in_array($channel, ['bank', 'qris'], true), 404);

        return view('customer.mobile.order-status', compact('order', 'settings', 'channel'));
    }

    public function reorder(Request $request, string $code): RedirectResponse
    {
        $order = $this->ownedOrder($request, $code);
        $products = $this->activeProducts()->keyBy('id');
        $cart = $request->session()->get('cart', []);
        $reorderPayload = [];

        foreach ($order->parsed_items as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $product = $productId === null ? null : $products->get($productId);

            if ($product === null || $product->stock < 1) {
                continue;
            }

            $newQuantity = min(((int) ($cart[$productId] ?? 0)) + $quantity, $product->stock);
            $cart[$productId] = $newQuantity;
            $reorderPayload[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'qty' => $quantity,
            ];
        }

        if ($reorderPayload === []) {
            return back()->withErrors(['cart' => 'Menu pada pesanan ini sedang tidak tersedia.']);
        }

        $request->session()->put('cart', $cart);

        return redirect()->route('mobile.cart')
            ->with('reorder_payload', $reorderPayload)
            ->with('status', "Menu dari pesanan {$order->code} ditambahkan ke keranjang.");
    }
}
