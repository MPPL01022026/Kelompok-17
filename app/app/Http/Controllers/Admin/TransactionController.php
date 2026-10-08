<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function manualSale(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'product_id' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::where('id', $data['product_id'])->where('active', true)->lockForUpdate()->first();
            if (!$product || $product->stock < $data['quantity']) {
                throw ValidationException::withMessages(['stock' => 'Stok produk tidak mencukupi.']);
            }

            $items = [
                [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $data['quantity'],
                    'price' => $product->price,
                ],
            ];

            Transaction::create([
                'id' => (string) Str::uuid(),
                'source' => 'manual',
                'customer_name' => $data['customer_name'],
                'items' => json_encode($items),
                'total' => $product->price * $data['quantity'],
            ]);

            $product->decrement('stock', $data['quantity']);
        });

        return redirect('/admin')->with('status', 'Transaksi dicatat.');
    }
}
