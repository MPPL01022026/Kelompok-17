<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AyamoController extends Controller
{
    private const PRODUCTS = [
        ['combo-original', 'Ayam Crispy Original', 'Ayam Goreng', 15000, 40, 'Ayam goreng crispy bumbu original, renyah di luar juicy di dalam.', '/img/combo-original.jpg'],
        ['combo-pedas', 'Ayam Crispy Pedas', 'Ayam Goreng', 16000, 35, 'Sensasi pedas gurih dengan racikan cabai pilihan Ayamo.', '/img/combo-pedas.jpg'],
        ['paket-nasi', 'Paket Nasi + Ayam', 'Paket Hemat', 22000, 30, '1 potong ayam crispy, nasi hangat, sambal, dan lalapan segar.', '/img/paket-nasi.jpg'],
        ['paket-combo', 'Combo Nasi + Ayam + Es Teh', 'Paket Hemat', 26000, 28, 'Paket lengkap: nasi, ayam crispy, sambal, dan es teh manis.', '/img/paket-combo.jpg'],
        ['sayap-crispy', 'Sayap Crispy (3 pcs)', 'Ayam Goreng', 18000, 25, 'Tiga potong sayap crispy renyah, cocok untuk cemilan.', '/img/sayap-crispy.jpg'],
        ['paket-keluarga', 'Paket Keluarga (5 pcs + 3 Nasi)', 'Paket Keluarga', 89000, 15, 'Lima potong ayam crispy, tiga porsi nasi, dan sambal spesial.', '/img/paket-keluarga.jpg'],
    ];

    public function shop(Request $request): View
    {
        $this->seedProducts();
        $products = DB::table('products')->where('active', true)->orderBy('name')->get();
        $cart = $request->session()->get('cart', []);
        $cartProducts = DB::table('products')->whereIn('id', array_keys($cart))->get()->keyBy('id');
        return view('shop', compact('products', 'cart', 'cartProducts'));
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'string']]);
        $product = DB::table('products')->where('id', $data['product_id'])->where('active', true)->first();
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
        $data = $request->validate(['quantities' => ['array'], 'quantities.*' => ['integer', 'min:0']]);
        $cart = [];
        foreach ($data['quantities'] ?? [] as $id => $quantity) {
            $product = DB::table('products')->where('id', $id)->where('active', true)->first();
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
            'customer_name' => ['required', 'string', 'max:120'], 'phone' => ['required', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:1000'], 'payment_method' => ['required', 'in:cod,whatsapp'],
        ]);
        $cart = $request->session()->get('cart', []);
        if (!$cart) {
            return back()->withErrors(['cart' => 'Keranjang masih kosong.']);
        }
        DB::transaction(function () use ($request, $data, $cart) {
            $items = [];
            $total = 0;
            foreach ($cart as $id => $quantity) {
                $product = DB::table('products')->where('id', $id)->where('active', true)->lockForUpdate()->first();
                if (!$product || $product->stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => 'Stok berubah. Periksa kembali isi keranjang.']);
                }
                $items[] = ['product_id' => $id, 'name' => $product->name, 'quantity' => $quantity, 'price' => $product->price];
                $total += $product->price * $quantity;
            }
            DB::table('orders')->insert([
                'id' => (string) Str::uuid(), 'customer_name' => $data['customer_name'], 'phone' => $data['phone'],
                'items' => json_encode($items), 'total' => $total, 'note' => $data['note'] ?? '',
                'payment_method' => $data['payment_method'], 'status' => 'pending', 'created_at' => now(), 'updated_at' => now(),
            ]);
            $request->session()->forget('cart');
        });
        return redirect('/')->with('status', 'Pesanan diterima. Admin akan segera mengonfirmasi.');
    }

    public function adminLogin(): View|RedirectResponse
    {
        return session()->has('admin') ? redirect('/admin') : view('admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate(['username' => ['required', 'string'], 'password' => ['required', 'string']]);
        $identifier = $data['username'];
        $attempt = DB::table('login_attempts')->where('identifier', $identifier)->first();
        if ($attempt?->locked_until && now()->lt($attempt->locked_until)) {
            return back()->withErrors(['login' => 'Terlalu banyak percobaan. Coba lagi beberapa menit.']);
        }
        $passwordHash = (string) env('ADMIN_PASSWORD_HASH');
        if ($identifier !== env('ADMIN_USERNAME') || $passwordHash === '' || !Hash::check($data['password'], $passwordHash)) {
            $attempts = ($attempt->attempts ?? 0) + 1;
            DB::table('login_attempts')->updateOrInsert(['identifier' => $identifier], [
                'attempts' => $attempts, 'locked_until' => $attempts >= 5 ? now()->addMinutes(15) : null,
                'updated_at' => now(), 'created_at' => $attempt->created_at ?? now(),
            ]);
            return back()->withErrors(['login' => 'Username atau password salah.']);
        }
        DB::table('login_attempts')->where('identifier', $identifier)->delete();
        $request->session()->regenerate();
        $request->session()->put('admin', $identifier);
        return redirect('/admin');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        $request->session()->regenerate();
        return redirect('/admin/login');
    }

    public function dashboard(Request $request): View
    {
        $this->seedProducts();
        $tab = $request->query('tab', 'overview');
        $period = $request->query('period', 'day');
        abort_unless(in_array($tab, ['overview', 'orders', 'products'], true), 404);
        abort_unless(in_array($period, ['day', 'week', 'month'], true), 400);
        $orders = DB::table('orders')->orderByDesc('created_at')->limit(200)->get();
        $products = DB::table('products')->where('active', true)->orderBy('name')->get();
        $transactions = DB::table('transactions')->orderBy('created_at')->get();
        $revenue = $transactions->sum('total');
        $units = $transactions->sum(fn ($transaction) => collect(json_decode($transaction->items, true) ?: [])->sum('quantity'));
        $grouped = $transactions->groupBy(function ($transaction) use ($period) {
            $date = \Illuminate\Support\Carbon::parse($transaction->created_at);
            return match ($period) {
                'month' => $date->format('Y-m'), 'week' => $date->format('o-\WW'), default => $date->format('Y-m-d'),
            };
        })->map(function ($rows, $key) use ($period) {
            $date = \Illuminate\Support\Carbon::parse($rows->first()->created_at);
            $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
            $label = $period === 'month' ? $months[(int) $date->format('n')] . ' ' . $date->format('Y') : ($period === 'week' ? 'Mgg ' . $date->format('W') : $date->format('j') . ' ' . $months[(int) $date->format('n')]);
            return ['date' => $key, 'label' => $label, 'revenue' => $rows->sum('total'), 'transactions' => $rows->count()];
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
            'revenue' => $revenue, 'units' => $units, 'transactions' => $transactions->count(),
            'chart' => $grouped->take(-($period === 'day' ? 14 : 12))->values(), 'best_sellers' => array_slice($bestSellers, 0, 5),
        ];
        return view('admin.dashboard', compact('tab', 'period', 'orders', 'products', 'stats'));
    }

    public function confirmOrder(string $id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            $order = DB::table('orders')->where('id', $id)->lockForUpdate()->first();
            abort_unless($order, 404);
            if ($order->status === 'confirmed') {
                return;
            }
            $items = json_decode($order->items, true) ?: [];
            foreach ($items as $item) {
                $product = DB::table('products')->where('id', $item['product_id'])->lockForUpdate()->first();
                if (!$product || !$product->active || $product->stock < $item['quantity']) {
                    throw ValidationException::withMessages(['stock' => 'Stok produk tidak mencukupi.']);
                }
            }
            DB::table('orders')->where('id', $id)->update(['status' => 'confirmed', 'updated_at' => now()]);
            foreach ($items as $item) {
                DB::table('products')->where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }
            DB::table('transactions')->insert([
                'id' => (string) Str::uuid(), 'source' => 'online', 'customer_name' => $order->customer_name,
                'items' => $order->items, 'total' => $order->total, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
        return back()->with('status', 'Pesanan dikonfirmasi.');
    }

    public function saveProduct(Request $request, ?string $id = null): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'], 'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0'], 'stock' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string'], 'image' => ['nullable', 'image', 'max:5120'], 'image_url' => ['nullable', 'string', 'max:500'],
        ]);
        $existing = $id ? DB::table('products')->where('id', $id)->first() : null;
        abort_if($id && !$existing, 404);
        $imageUrl = $existing->image_url ?? ($data['image_url'] ?? '');
        if ($request->hasFile('image')) {
            $imageUrl = '/storage/' . $request->file('image')->store('products', 'public');
        }
        $values = [
            'name' => $data['name'], 'category' => $data['category'], 'price' => $data['price'], 'stock' => $data['stock'],
            'description' => $data['description'], 'image_url' => $imageUrl, 'active' => true, 'updated_at' => now(),
        ];
        if ($existing) {
            DB::table('products')->where('id', $id)->update($values);
        } else {
            DB::table('products')->insert($values + ['id' => (string) Str::uuid(), 'created_at' => now()]);
        }
        return redirect('/admin?tab=products')->with('status', 'Produk disimpan.');
    }

    public function deleteProduct(string $id): RedirectResponse
    {
        DB::table('products')->where('id', $id)->update(['active' => false, 'updated_at' => now()]);
        return back()->with('status', 'Produk diarsipkan.');
    }

    public function manualSale(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'], 'product_id' => ['required', 'string'], 'quantity' => ['required', 'integer', 'min:1'],
        ]);
        DB::transaction(function () use ($data) {
            $product = DB::table('products')->where('id', $data['product_id'])->where('active', true)->lockForUpdate()->first();
            if (!$product || $product->stock < $data['quantity']) {
                throw ValidationException::withMessages(['stock' => 'Stok produk tidak mencukupi.']);
            }
            $items = [['product_id' => $product->id, 'name' => $product->name, 'quantity' => $data['quantity'], 'price' => $product->price]];
            DB::table('transactions')->insert([
                'id' => (string) Str::uuid(), 'source' => 'manual', 'customer_name' => $data['customer_name'],
                'items' => json_encode($items), 'total' => $product->price * $data['quantity'], 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('products')->where('id', $product->id)->decrement('stock', $data['quantity']);
        });
        return redirect('/admin')->with('status', 'Transaksi dicatat.');
    }

    private function seedProducts(): void
    {
        foreach (self::PRODUCTS as [$id, $name, $category, $price, $stock, $description, $image]) {
            if (!DB::table('products')->where('id', $id)->exists()) {
                DB::table('products')->insert([
                    'id' => $id, 'name' => $name, 'category' => $category, 'price' => $price, 'stock' => $stock,
                    'description' => $description, 'image_url' => $image, 'active' => true, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
}
