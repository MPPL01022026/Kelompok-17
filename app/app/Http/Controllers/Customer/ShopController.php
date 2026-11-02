<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ShopController extends Controller
{
    private function getActiveProducts(): Collection
    {
        Product::seedDefaults();

        try {
            $products = Product::where('active', true)->get();
            if ($products->isNotEmpty()) {
                $priority = [
                    'dada-ayam' => 1,
                    'sayap-ayam' => 2,
                    'paha-atas' => 3,
                    'paha-bawah' => 4,
                    'ceker-ayam' => 5,
                    'hati-ayam' => 6,
                    'leher-ayam' => 7,
                    'bakso-goreng-stick' => 8,
                ];

                $iconMap = collect(Product::DEFAULT_PRODUCTS)->pluck('image_url', 'id')->all();

                return $products->map(function (Product $p) use ($iconMap): Product {
                    if (empty($p->image_url) || str_ends_with($p->image_url, '.svg') || $p->image_url === '/img/drumstick.svg') {
                        $p->image_url = $iconMap[$p->id] ?? $p->image_url;
                    }
                    return $p;
                })->sortBy(fn(Product $p) => $priority[$p->id] ?? 99)->values();
            }
        } catch (\Throwable) {
            // Fallback to default products collection if DB is offline
        }

        return collect(Product::DEFAULT_PRODUCTS)->map(fn(array $item) => new Product($item + ['active' => true]));
    }

    private function getCartData(Request $request, ?Collection $allProducts = null): array
    {
        $products = $allProducts ?? $this->getActiveProducts();
        $cart = $request->session()->get('cart', []);
        $cartProducts = $products->whereIn('id', array_keys($cart))->keyBy('id');

        $cartCount = 0;
        $cartTotal = 0;
        foreach ($cart as $id => $qty) {
            if ($cartProducts->has($id) && $qty > 0) {
                $cartCount += $qty;
                $cartTotal += $cartProducts[$id]->price * $qty;
            }
        }

        return [$cart, $cartProducts, $cartCount, $cartTotal];
    }

    private function seedDefaultOrders(): void
    {
        try {
            if (Order::count() === 0) {
                Order::firstOrCreate(
                    ['id' => 'AYM-2041'],
                    [
                        'customer_name' => 'Pelanggan Ayamo',
                        'phone' => '0895346200506',
                        'items' => json_encode([
                            ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'quantity' => 1, 'price' => 7000],
                            ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'quantity' => 1, 'price' => 1000],
                        ]),
                        'total' => 8000,
                        'note' => '',
                        'payment_method' => 'cod',
                        'status' => 'confirmed',
                    ]
                );

                Order::firstOrCreate(
                    ['id' => 'AYM-9740'],
                    [
                        'customer_name' => 'Pelanggan Ayamo',
                        'phone' => '0895346200506',
                        'items' => json_encode([
                            ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'quantity' => 1, 'price' => 7000],
                            ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'quantity' => 1, 'price' => 7000],
                        ]),
                        'total' => 14000,
                        'note' => 'Ambil pukul 19.00',
                        'payment_method' => 'whatsapp',
                        'status' => 'pending',
                    ]
                );
            }
        } catch (\Throwable) {
            // Ignore if DB is unreachable
        }
    }

    private function findOrderOrFallback(Request $request, string $code): Order
    {
        $this->seedDefaultOrders();

        try {
            $order = Order::where('id', $code)->first();
            if ($order) {
                return $order;
            }
        } catch (\Throwable) {
            // Check session fallback
        }

        $sessionOrders = $request->session()->get('mobile_orders_data', []);
        if (isset($sessionOrders[$code])) {
            return new Order($sessionOrders[$code]);
        }

        if ($code === 'AYM-2041') {
            return new Order([
                'id' => 'AYM-2041',
                'customer_name' => 'Pelanggan Ayamo',
                'phone' => '0895346200506',
                'items' => json_encode([
                    ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'quantity' => 1, 'price' => 7000],
                    ['product_id' => 'bakso-goreng-stick', 'name' => 'Bakso Goreng Stick', 'quantity' => 1, 'price' => 1000],
                ]),
                'total' => 8000,
                'note' => '',
                'payment_method' => 'cod',
                'status' => 'confirmed',
            ]);
        }

        return new Order([
            'id' => $code,
            'customer_name' => 'Pelanggan Ayamo',
            'phone' => '0895346200506',
            'items' => json_encode([
                ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'quantity' => 1, 'price' => 7000],
                ['product_id' => 'paha-atas', 'name' => 'Paha Atas', 'quantity' => 1, 'price' => 7000],
            ]),
            'total' => 14000,
            'note' => '',
            'payment_method' => 'whatsapp',
            'status' => 'pending',
        ]);
    }

    public function shopWeb(Request $request): View
    {
        $products = $this->getActiveProducts();
        [$cart, $cartProducts] = $this->getCartData($request, $products);

        return view('customer.web.shop', compact('products', 'cart', 'cartProducts'));
    }

    public function shopMobile(Request $request): View
    {
        $products = $this->getActiveProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->getCartData($request, $products);
        $settings = Settings::getMap();

        $favorites = $products->whereIn('id', ['dada-ayam', 'paha-atas', 'sayap-ayam', 'paha-bawah', 'ceker-ayam', 'bakso-goreng-stick'])->values();
        if ($favorites->isEmpty()) {
            $favorites = $products->take(6);
        }

        return view('customer.mobile.home', compact('products', 'favorites', 'cart', 'cartProducts', 'cartCount', 'cartTotal', 'settings'));
    }

    public function mobileSplash(): View
    {
        return view('customer.mobile.splash');
    }

    public function shopMenu(Request $request): View
    {
        $allProducts = $this->getActiveProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->getCartData($request, $allProducts);

        $search = trim((string) $request->query('q', ''));
        $products = $allProducts;

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $products = $allProducts->filter(function (Product $product) use ($needle): bool {
                return str_contains(mb_strtolower($product->name), $needle)
                    || str_contains(mb_strtolower($product->description), $needle)
                    || str_contains(mb_strtolower($product->category), $needle);
            })->values();
        }

        return view('customer.mobile.menu.shop-menu', compact('products', 'search', 'cart', 'cartProducts', 'cartCount', 'cartTotal'));
    }

    public function mobileCart(Request $request): View
    {
        $products = $this->getActiveProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->getCartData($request, $products);
        $settings = Settings::getMap();

        return view('customer.mobile.cart', compact('products', 'cart', 'cartProducts', 'cartCount', 'cartTotal', 'settings'));
    }

    public function mobilePayment(Request $request, string $code): View
    {
        $order = $this->findOrderOrFallback($request, $code);
        $settings = Settings::getMap();
        $channel = $request->query('channel', 'bank');

        return view('customer.mobile.payment', compact('order', 'settings', 'channel'));
    }

    public function mobileOrderStatus(Request $request, string $code): View
    {
        $order = $this->findOrderOrFallback($request, $code);
        $settings = Settings::getMap();
        $channel = $request->query('channel', 'bank');

        return view('customer.mobile.order-status', compact('order', 'settings', 'channel'));
    }

    public function mobileHistory(Request $request): View
    {
        $this->seedDefaultOrders();
        $products = $this->getActiveProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->getCartData($request, $products);

        try {
            $orders = Order::orderByDesc('created_at')->limit(30)->get();
        } catch (\Throwable) {
            $orders = collect();
        }

        if ($orders->isEmpty()) {
            $orders = collect([
                $this->findOrderOrFallback($request, 'AYM-2041'),
                $this->findOrderOrFallback($request, 'AYM-9740'),
            ]);
        }

        return view('customer.mobile.history', compact('orders', 'cart', 'cartProducts', 'cartCount', 'cartTotal'));
    }

    public function mobileReorder(Request $request, string $code): RedirectResponse
    {
        $order = $this->findOrderOrFallback($request, $code);
        $products = $this->getActiveProducts()->keyBy('id');
        $cart = $request->session()->get('cart', []);
        $reorderPayload = [];

        foreach ($order->parsed_items as $item) {
            $pid = $item['product_id'] ?? null;
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            if ($pid && $products->has($pid)) {
                $p = $products[$pid];
                $maxStock = max(1, (int) $p->stock);
                $cart[$pid] = min(($cart[$pid] ?? 0) + $qty, $maxStock);
                $reorderPayload[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (int) $p->price,
                    'stock' => $maxStock,
                    'qty' => $qty,
                ];
            }
        }

        $request->session()->put('cart', $cart);

        return redirect('/mobile/cart')
            ->with('reorder_payload', $reorderPayload)
            ->with('status', 'Menu dari pesanan ' . $order->code . ' ditambahkan ke keranjang.');
    }

    public function addToCart(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'string'],
        ]);

        $products = $this->getActiveProducts();
        $product = $products->firstWhere('id', $data['product_id']);
        if (!$product || $product->stock < 1) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Produk sedang habis.'], 422);
            }
            return back()->with('error', 'Produk sedang habis.')->withErrors(['cart' => 'Produk sedang habis.']);
        }

        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + 1, $product->stock);
        $request->session()->put('cart', $cart);

        if ($request->expectsJson()) {
            [, , $cartCount, $cartTotal] = $this->getCartData($request, $products);
            return response()->json([
                'ok' => true,
                'productId' => $product->id,
                'itemQty' => $cart[$product->id],
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
                'cartTotalFormatted' => number_format($cartTotal, 0, ',', '.'),
                'message' => $product->name . ' ditambahkan ke keranjang.',
            ]);
        }

        return back()->with('status', $product->name . ' ditambahkan ke keranjang.');
    }

    public function stepCart(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'string'],
            'action' => ['required', 'in:inc,dec'],
        ]);

        $products = $this->getActiveProducts();
        $product = $products->firstWhere('id', $data['product_id']);
        $cart = $request->session()->get('cart', []);
        $current = (int) ($cart[$data['product_id']] ?? 0);

        if ($data['action'] === 'inc' && $product) {
            $cart[$product->id] = min($current + 1, max(1, $product->stock));
        } elseif ($data['action'] === 'dec') {
            if ($current <= 1) {
                unset($cart[$data['product_id']]);
            } else {
                $cart[$data['product_id']] = $current - 1;
            }
        }

        $request->session()->put('cart', $cart);

        if ($request->expectsJson()) {
            [, , $cartCount, $cartTotal] = $this->getCartData($request, $products);
            return response()->json([
                'ok' => true,
                'productId' => $data['product_id'],
                'itemQty' => (int) ($cart[$data['product_id']] ?? 0),
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal,
                'cartTotalFormatted' => number_format($cartTotal, 0, ',', '.'),
            ]);
        }

        return back();
    }

    public function updateCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quantities' => ['array'],
            'quantities.*' => ['integer', 'min:0'],
        ]);

        $products = $this->getActiveProducts()->keyBy('id');
        $cart = [];
        foreach ($data['quantities'] ?? [] as $id => $quantity) {
            if ($products->has($id) && $quantity > 0) {
                $cart[$id] = min($quantity, $products[$id]->stock);
            }
        }

        $request->session()->put('cart', $cart);
        return back();
    }

    public function checkout(Request $request): RedirectResponse
    {
        return $this->processCheckout($request, false);
    }

    public function mobileCheckout(Request $request): RedirectResponse
    {
        return $this->processCheckout($request, true);
    }

    private function processCheckout(Request $request, bool $isMobile): RedirectResponse
    {
        $request->merge([
            'customer_name' => trim((string) $request->input('customer_name', '')),
            'phone' => preg_replace('/[\s\-]+/', '', (string) $request->input('phone', '')),
        ]);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,12}$/'],
            'note' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,whatsapp,tunai,transfer'],
            'items' => ['nullable', 'array', 'max:30'],
            'items.*' => ['integer', 'min:1', 'max:100'],
        ], [
            'customer_name.required' => 'Nama pemesan wajib diisi.',
            'customer_name.min' => 'Nama pemesan minimal 2 karakter.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid (gunakan 08xx atau 628xx).',
            'items.*.min' => 'Jumlah item minimal 1.',
            'items.*.max' => 'Jumlah per item maksimal 100.',
        ]);

        // Ambil daftar item dari payload checkout (client-side cart) atau fallback session cart
        $submittedItems = collect($data['items'] ?? [])
            ->mapWithKeys(fn ($qty, $id) => [(string) $id => max(0, (int) $qty)])
            ->filter(fn (int $qty) => $qty > 0)
            ->all();

        $cart = !empty($submittedItems) ? $submittedItems : $request->session()->get('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.')->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        // Validasi produk terhadap katalog aktif & stok sebelum transaksi
        $catalog = $this->getActiveProducts()->keyBy('id');
        foreach ($cart as $id => $quantity) {
            if (!$catalog->has($id)) {
                return back()->with('error', 'Terdapat produk yang tidak valid di keranjang.')->withErrors(['cart' => 'Produk tidak ditemukan.']);
            }
            if ($quantity > $catalog[$id]->stock) {
                return back()->with('error', "Stok {$catalog[$id]->name} tersisa {$catalog[$id]->stock} porsi.")->withErrors(['cart' => "Stok {$catalog[$id]->name} tidak mencukupi."]);
            }
        }

        $normalizedMethod = in_array($data['payment_method'], ['cod', 'tunai'], true) ? 'cod' : 'whatsapp';
        $orderCode = Order::generateCode();

        try {
            DB::transaction(function () use ($request, $data, $cart, $normalizedMethod, $orderCode) {
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

                $orderData = [
                    'id' => $orderCode,
                    'customer_name' => $data['customer_name'],
                    'phone' => $data['phone'],
                    'items' => json_encode($items),
                    'total' => $total,
                    'note' => $data['note'] ?? '',
                    'payment_method' => $normalizedMethod,
                    'status' => 'pending',
                ];

                Order::create($orderData);

                $sessionOrders = $request->session()->get('mobile_orders_data', []);
                $sessionOrders[$orderCode] = $orderData;
                $request->session()->put('mobile_orders_data', $sessionOrders);
                $request->session()->forget('cart');
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable) {
            // Fallback to session storage if DB is offline
            $products = $this->getActiveProducts()->keyBy('id');
            $items = [];
            $total = 0;
            foreach ($cart as $id => $quantity) {
                if ($products->has($id)) {
                    $p = $products[$id];
                    $items[] = [
                        'product_id' => $id,
                        'name' => $p->name,
                        'quantity' => $quantity,
                        'price' => $p->price,
                    ];
                    $total += $p->price * $quantity;
                }
            }

            $orderData = [
                'id' => $orderCode,
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'items' => json_encode($items),
                'total' => $total,
                'note' => $data['note'] ?? '',
                'payment_method' => $normalizedMethod,
                'status' => 'pending',
            ];

            $sessionOrders = $request->session()->get('mobile_orders_data', []);
            $sessionOrders[$orderCode] = $orderData;
            $request->session()->put('mobile_orders_data', $sessionOrders);
            $request->session()->forget('cart');
        }

        if ($isMobile) {
            return redirect('/mobile/payment/' . $orderCode)->with('clear_mobile_cart', true);
        }

        return redirect('/')->with('status', 'Pesanan ' . $orderCode . ' diterima. Admin akan segera mengonfirmasi.');
    }
}
