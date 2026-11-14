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
            'product_id' => ['required_without:items', 'nullable', 'string'],
            'quantity' => ['required_without:items', 'nullable', 'integer', 'min:1'],
            'items' => ['required_without:product_id', 'nullable', 'array', 'min:1', 'max:30'],
            'items.*.product_id' => ['required_with:items', 'string'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1', 'max:500'],
        ]);

        $requestedMap = [];
        if (! empty($data['items'])) {
            foreach ($data['items'] as $row) {
                $pid = (string) $row['product_id'];
                $requestedMap[$pid] = ($requestedMap[$pid] ?? 0) + (int) $row['quantity'];
            }
        } else {
            $requestedMap[(string) $data['product_id']] = (int) $data['quantity'];
        }
        ksort($requestedMap);

        DB::transaction(function () use ($data, $requestedMap): void {
            $products = Product::query()
                ->whereIn('id', array_keys($requestedMap))
                ->where('active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($requestedMap as $pid => $qty) {
                $product = $products->get($pid);
                if (! $product || $product->stock < $qty) {
                    throw ValidationException::withMessages(['stock' => 'Stok produk tidak mencukupi.']);
                }
            }

            $items = [];
            $total = 0;

            foreach ($requestedMap as $pid => $qty) {
                $product = $products->get($pid);
                $items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $qty,
                    'price' => $product->price,
                ];
                $total += $product->price * $qty;
                $product->decrement('stock', $qty);
            }

            Transaction::create([
                'id' => (string) Str::uuid(),
                'source' => 'manual',
                'customer_name' => $data['customer_name'],
                'items' => json_encode($items),
                'total' => $total,
            ]);
        });

        return redirect('/admin')->with('status', 'Transaksi dicatat.');
    }
}
