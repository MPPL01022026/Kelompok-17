<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function shop(Request $request): View
    {
        Product::seedDefaults();

        $products = Product::where('active', true)->orderBy('name')->get();
        $cart = $request->session()->get('cart', []);
        $cartProducts = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return view('shop', compact('products', 'cart', 'cartProducts'));
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'string'],
        ]);

        $product = Product::where('id', $data['product_id'])->where('active', true)->first();
        if (!$product || $product->stock < 1) {
            return back()->withErrors(['cart' => 'Produk sedang habis.']);
        }

        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + 1, $product->stock);
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Produk ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quantities' => ['array'],
            'quantities.*' => ['integer', 'min:0'],
        ]);

        $cart = [];
        foreach ($data['quantities'] ?? [] as $id => $quantity) {
            $product = Product::where('id', $id)->where('active', true)->first();
            if ($product && $quantity > 0) {
                $cart[$id] = min($quantity, $product->stock);
            }
        }

        $request->session()->put('cart', $cart);
        return back();
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,whatsapp'],
        ]);

        $cart = $request->session()->get('cart', []);
        if (!$cart) {
            return back()->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        DB::transaction(function () use ($request, $data, $cart) {
            $items = [];
            $total = 0;

            foreach ($cart as $id => $quantity) {
                $product = Product::where('id', $id)->where('active', true)->lockForUpdate()->first();
                if (!$product || $product->stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => 'Stok berubah. Periksa kembali isi keranjang.']);
                }

                $items[] = [
                    'product_id' => $id,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ];
                $total += $product->price * $quantity;
            }

            Order::create([
                'id' => (string) Str::uuid(),
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'items' => json_encode($items),
                'total' => $total,
                'note' => $data['note'] ?? '',
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
            ]);

            $request->session()->forget('cart');
        });

        return redirect('/')->with('status', 'Pesanan diterima. Admin akan segera mengonfirmasi.');
    }
}
