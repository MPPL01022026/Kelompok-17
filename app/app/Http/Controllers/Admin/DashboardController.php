<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        Product::seedDefaults();

        $tab = $request->query('tab', 'overview');
        $period = $request->query('period', 'day');

        abort_unless(in_array($tab, ['overview', 'orders', 'products'], true), 404);
        abort_unless(in_array($period, ['day', 'week', 'month'], true), 400);

        $orders = Order::orderByDesc('created_at')->limit(200)->get();
        $products = Product::where('active', true)->orderBy('name')->get();
        $transactions = Transaction::orderBy('created_at')->get();

        $revenue = $transactions->sum('total');
        $units = $transactions->sum(fn ($transaction) => collect(json_decode($transaction->items, true) ?: [])->sum('quantity'));

        $grouped = $transactions->groupBy(function ($transaction) use ($period) {
            $date = Carbon::parse($transaction->created_at);
            return match ($period) {
                'month' => $date->format('Y-m'),
                'week' => $date->format('o-\WW'),
                default => $date->format('Y-m-d'),
            };
        })->map(function ($rows, $key) use ($period) {
            $date = Carbon::parse($rows->first()->created_at);
            $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
            $label = $period === 'month' 
                ? $months[(int) $date->format('n')] . ' ' . $date->format('Y') 
                : ($period === 'week' ? 'Mgg ' . $date->format('W') : $date->format('j') . ' ' . $months[(int) $date->format('n')]);

            return [
                'date' => $key,
                'label' => $label,
                'revenue' => $rows->sum('total'),
                'transactions' => $rows->count(),
            ];
        });

        $bestSellers = [];
        foreach ($transactions as $transaction) {
            foreach (json_decode($transaction->items, true) ?: [] as $item) {
                $id = $item['product_id'];
                $bestSellers[$id] ??= ['product_id' => $id, 'name' => $item['name'], 'units' => 0, 'revenue' => 0];
                $bestSellers[$id]['units'] += $item['quantity'];
                $bestSellers[$id]['revenue'] += $item['price'] * $item['quantity'];
            }
        }
        usort($bestSellers, fn ($a, $b) => [$b['units'], $b['revenue']] <=> [$a['units'], $a['revenue']]);

        $stats = [
            'revenue' => $revenue,
            'units' => $units,
            'transactions' => $transactions->count(),
            'chart' => $grouped->take(-($period === 'day' ? 14 : 12))->values(),
            'best_sellers' => array_slice($bestSellers, 0, 5),
        ];

        return view('admin.dashboard', compact('tab', 'period', 'orders', 'products', 'stats'));
    }
}
