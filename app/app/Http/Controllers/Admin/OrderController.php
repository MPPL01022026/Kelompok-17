<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function confirm(string $id): RedirectResponse
    {
        $message = $this->applyStatusTransition($id, 'confirmed');

        return back()->with('status', $message);
    }

    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,processing,ready,confirmed,cancelled'],
        ]);

        $message = $this->applyStatusTransition($id, $data['status']);

        return back()->with('status', $message);
    }

    private function applyStatusTransition(string $id, string $targetStatus): string
    {
        return DB::transaction(function () use ($id, $targetStatus): string {
            $order = Order::query()->where('id', $id)->lockForUpdate()->first();
            abort_unless($order, 404);

            $previousStatus = (string) $order->status;

            if ($targetStatus === 'cancelled') {
                if ($previousStatus === 'cancelled') {
                    return 'Pesanan sudah dibatalkan.';
                }

                if ($order->stock_reserved) {
                    $items = collect($order->parsed_items)->sortBy('product_id')->values();
                    $products = Product::query()
                        ->whereIn('id', $items->pluck('product_id')->filter())
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    foreach ($items as $item) {
                        $product = $products->get($item['product_id'] ?? null);
                        $qty = max(0, (int) ($item['quantity'] ?? 0));
                        if ($product && $qty > 0) {
                            $product->increment('stock', $qty);
                        }
                    }
                }

                $order->update([
                    'status' => 'cancelled',
                    'stock_reserved' => false,
                ]);

                Transaction::query()->where('id', 'order-' . $order->id)->delete();

                return 'Pesanan dibatalkan dan stok dikembalikan.';
            }

            if ($targetStatus === 'pending') {
                if (! $order->stock_reserved) {
                    $this->reserveOrderStock($order);
                }

                $order->update([
                    'status' => 'pending',
                    'stock_reserved' => true,
                ]);

                Transaction::query()->where('id', 'order-' . $order->id)->delete();

                return 'Status pesanan diperbarui.';
            }

            if ($previousStatus === 'confirmed' && $targetStatus === 'confirmed') {
                return 'Pesanan dikonfirmasi.';
            }

            if (! $order->stock_reserved) {
                $this->reserveOrderStock($order);
            }

            $order->update([
                'status' => $targetStatus,
                'stock_reserved' => true,
            ]);

            if ($targetStatus === 'confirmed') {
                Transaction::query()->updateOrCreate(
                    ['id' => 'order-' . $order->id],
                    [
                        'source' => 'online',
                        'customer_name' => $order->customer_name,
                        'items' => is_string($order->items) ? $order->items : json_encode($order->items),
                        'total' => $order->total,
                    ]
                );

                return 'Pesanan dikonfirmasi.';
            }

            Transaction::query()->where('id', 'order-' . $order->id)->delete();

            return 'Status pesanan diperbarui.';
        });
    }

    private function reserveOrderStock(Order $order): void
    {
        $items = collect($order->parsed_items)->sortBy('product_id')->values();
        $products = Product::query()
            ->whereIn('id', $items->pluck('product_id')->filter())
            ->where('active', true)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item['product_id'] ?? null);
            $qty = max(0, (int) ($item['quantity'] ?? 0));

            if ($product === null || $product->stock < $qty) {
                throw ValidationException::withMessages(['stock' => 'Stok produk tidak mencukupi.']);
            }
        }

        foreach ($items as $item) {
            $product = $products->get($item['product_id'] ?? null);
            $qty = max(0, (int) ($item['quantity'] ?? 0));
            if ($product && $qty > 0) {
                $product->stock -= $qty;
                $product->save();
            }
        }
    }
}
