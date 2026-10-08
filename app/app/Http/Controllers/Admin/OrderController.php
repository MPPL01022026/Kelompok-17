<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function confirm(string $id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            $order = Order::where('id', $id)->lockForUpdate()->first();
            abort_unless($order, 404);

            if ($order->status === 'confirmed') {
                return;
            }

            $items = json_decode($order->items, true) ?: [];
            foreach ($items as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();
                if (!$product || !$product->active || $product->stock < $item['quantity']) {
                    throw ValidationException::withMessages(['stock' => 'Stok produk tidak mencukupi.']);
                }
            }

            $order->update(['status' => 'confirmed']);

            foreach ($items as $item) {
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            Transaction::create([
                'id' => (string) Str::uuid(),
                'source' => 'online',
                'customer_name' => $order->customer_name,
                'items' => is_string($order->items) ? $order->items : json_encode($order->items),
                'total' => $order->total,
            ]);
        });

        return back()->with('status', 'Pesanan dikonfirmasi.');
    }
}
