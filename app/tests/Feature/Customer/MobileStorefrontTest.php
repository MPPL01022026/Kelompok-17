<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function createMobileProducts(): void
{
    foreach (Product::DEFAULT_PRODUCTS as $product) {
        Product::query()->create($product + ['active' => true]);
    }
}

it('renders the mobile catalog and filters menu products', function () {
    createMobileProducts();

    $this->get(route('mobile.home'))
        ->assertOk()
        ->assertSee('Menu Favorit')
        ->assertSee('Dada Ayam');

    $this->get(route('mobile.menu', ['q' => 'bakso']))
        ->assertOk()
        ->assertSee('Bakso Goreng Stick')
        ->assertDontSee('Dada Ayam');
});

it('creates an order from server-validated catalog prices', function () {
    createMobileProducts();
    $checkoutToken = (string) Str::uuid();

    $response = $this->withSession(['customer_checkout_token' => $checkoutToken])->post(route('mobile.checkout'), [
        'customer_name' => 'Rifki',
        'phone' => '081234567890',
        'note' => 'Ambil pukul 19.00',
        'payment_method' => 'transfer',
        'checkout_token' => $checkoutToken,
        'items' => ['dada-ayam' => 2],
    ]);

    $order = Order::query()->sole();
    $response->assertRedirect(route('mobile.payment', $order->id));
    $response->assertSessionHas('mobile_order_codes', fn (array $codes): bool => in_array($order->id, $codes, true));
    expect($order->total)->toBe(14000)
        ->and($order->payment_method)->toBe('whatsapp')
        ->and($order->stock_reserved)->toBeTrue()
        ->and(Product::query()->findOrFail('dada-ayam')->stock)->toBe(48);
});

it('rejects invalid checkout details without creating an order', function () {
    createMobileProducts();
    $checkoutToken = (string) Str::uuid();

    $this->withSession(['customer_checkout_token' => $checkoutToken])->from(route('mobile.cart'))
        ->post(route('mobile.checkout'), [
            'customer_name' => 'R',
            'phone' => '12345',
            'payment_method' => 'cod',
            'checkout_token' => $checkoutToken,
            'items' => ['dada-ayam' => 1],
        ])
        ->assertRedirect(route('mobile.cart'))
        ->assertSessionHasErrors(['customer_name', 'phone']);

    expect(Order::query()->count())->toBe(0);
});

it('does not expose an order code that is not owned by the device session', function () {
    createMobileProducts();
    Order::query()->create([
        'id' => 'AYM-1111',
        'customer_name' => 'Pelanggan Lain',
        'phone' => '081234567890',
        'items' => json_encode([]),
        'total' => 7000,
        'note' => '',
        'payment_method' => 'cod',
        'status' => 'pending',
    ]);

    $this->get(route('mobile.orders.status', 'AYM-1111'))
        ->assertNotFound();
});

it('uses the latest database status for an order owned by the device session', function () {
    createMobileProducts();
    $orderData = [
        'id' => 'AYM-1111',
        'customer_name' => 'Rifki',
        'phone' => '081234567890',
        'items' => json_encode([]),
        'total' => 7000,
        'note' => '',
        'payment_method' => 'cod',
        'status' => 'confirmed',
    ];
    Order::query()->create($orderData);
    $staleSessionOrder = array_replace($orderData, ['status' => 'pending']);

    $this->withSession([
        'mobile_order_codes' => ['AYM-1111'],
        'mobile_orders_data' => ['AYM-1111' => $staleSessionOrder],
    ])->get(route('mobile.orders.status', 'AYM-1111'))
        ->assertOk()
        ->assertViewHas('order', fn (Order $order): bool => $order->status === 'confirmed');
});

it('shows only orders remembered by the current device session', function () {
    createMobileProducts();

    foreach (['AYM-1111' => 'Pesanan Saya', 'AYM-2222' => 'Pelanggan Lain'] as $code => $customer) {
        Order::query()->create([
            'id' => $code,
            'customer_name' => $customer,
            'phone' => '081234567890',
            'items' => json_encode([
                ['product_id' => 'dada-ayam', 'name' => 'Dada Ayam', 'quantity' => 1, 'price' => 7000],
            ]),
            'total' => 7000,
            'note' => '',
            'payment_method' => 'cod',
            'status' => 'pending',
        ]);
    }

    $this->withSession(['mobile_order_codes' => ['AYM-1111']])
        ->get(route('mobile.history'))
        ->assertOk()
        ->assertSee('AYM-1111')
        ->assertDontSee('AYM-2222');
});
