<?php

namespace App\Http\Controllers\Customer;

use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileCartController extends MobileStorefrontController
{
    public function show(Request $request): View
    {
        $products = $this->activeProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->cartData($request, $products);
        $settings = Settings::getMap();
        $checkoutToken = $this->checkoutToken($request);

        return view('customer.mobile.cart', compact(
            'products', 'cart', 'cartProducts', 'cartCount', 'cartTotal', 'settings', 'checkoutToken',
        ));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate(['product_id' => ['required', 'string', 'max:100']]);
        $products = $this->activeProducts();
        $product = $products->firstWhere('id', $data['product_id']);

        if ($product === null || $product->stock < 1) {
            return $this->cartError($request, 'Produk sedang habis.');
        }

        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = min(((int) ($cart[$product->id] ?? 0)) + 1, $product->stock);
        $request->session()->put('cart', $cart);

        if ($request->expectsJson()) {
            [, , $cartCount, $cartTotal] = $this->cartData($request, $products);

            return response()->json([
                'ok' => true,
                'productId' => $product->id,
                'itemQty' => $cart[$product->id],
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
                'cartTotalFormatted' => number_format($cartTotal, 0, ',', '.'),
                'message' => $product->name.' ditambahkan ke keranjang.',
            ]);
        }

        return back()->with('status', $product->name.' ditambahkan ke keranjang.');
    }

    public function step(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'string', 'max:100'],
            'action' => ['required', 'in:inc,dec'],
        ]);
        $products = $this->activeProducts();
        $product = $products->firstWhere('id', $data['product_id']);

        if ($product === null) {
            return $this->cartError($request, 'Produk tidak ditemukan.');
        }

        $cart = $request->session()->get('cart', []);
        $currentQuantity = (int) ($cart[$product->id] ?? 0);

        if ($data['action'] === 'inc') {
            if ($product->stock < 1) {
                return $this->cartError($request, 'Produk sedang habis.');
            }

            $cart[$product->id] = min($currentQuantity + 1, $product->stock);
        } elseif ($currentQuantity <= 1) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $currentQuantity - 1;
        }

        $request->session()->put('cart', $cart);

        if ($request->expectsJson()) {
            [, , $cartCount, $cartTotal] = $this->cartData($request, $products);

            return response()->json([
                'ok' => true,
                'productId' => $product->id,
                'itemQty' => (int) ($cart[$product->id] ?? 0),
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
                'cartTotalFormatted' => number_format($cartTotal, 0, ',', '.'),
            ]);
        }

        return back();
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quantities' => ['sometimes', 'array', 'max:30'],
            'quantities.*' => ['integer', 'min:0', 'max:100'],
        ]);
        $products = $this->activeProducts()->keyBy('id');
        $cart = [];

        foreach ($data['quantities'] ?? [] as $productId => $quantity) {
            $product = $products->get($productId);

            if ($product !== null && $quantity > 0 && $product->stock > 0) {
                $cart[$productId] = min($quantity, $product->stock);
            }
        }

        $request->session()->put('cart', $cart);

        return back();
    }

    private function cartError(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $message], 422);
        }

        return back()->with('error', $message)->withErrors(['cart' => $message]);
    }
}
