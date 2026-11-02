<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
  public function __invoke(Request $request): RedirectResponse
  {
    $request->validate([
      'payment_method' => 'required',
    ]);

    $sessionCart = $request->session()->get('cart', []);
    $cart = array_filter($sessionCart, fn($qty) => $qty > 0);
    if (empty($cart)) {
      return back()->with('error', 'Keranjang masih kosong.');
    }

    return DB::transaction(function () use ($request, $cart) {
      $products = collect(Product::DEFAULT_PRODUCTS)->keyBy('id');

      $totalPrice = 0;
      $orderItems = [];

      foreach ($cart as $productId => $qty) {
        $product = $products->get($productId);
        if (!$product) {
          continue;
        }

        $itemTotal = $product['price'] * $qty;
        $totalPrice += $itemTotal;

        $orderItems[] = [
          'product_id' => $productId,
          'product_name' => $product['name'],
          'qty' => $qty,
          'price' => $product['price'],
          'subtotal' => $itemTotal,
        ];
      }

      $orderCode = 'ORD-' . strtoupper(Str::random(6));
      while (Order::where('code', $orderCode)->exists()) {
        $orderCode = 'ORD-' . strtoupper(Str::random(6));
      }

      $order = Order::create([
        'code' => $orderCode,
        'total_price' => $totalPrice,
        'status' => 'pending',
        'payment_method' => $request->input('payment_method'),
        'session_id' => $request->session()->getId(),
        'cart_payload' => json_encode($orderItems),
      ]);

      $request->session()->remove('cart');
      $request->session()->put('last_order_code', $orderCode);

      return redirect('/order/' . $orderCode);
    });
  }


}
