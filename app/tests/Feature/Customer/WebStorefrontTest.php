<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function createWebProducts(): void
{
    foreach (Product::DEFAULT_PRODUCTS as $product) {
        Product::query()->create($product + ['active' => true]);
    }
}

it('renders the polished web home and searchable catalog', function () {
    createWebProducts();

    $this->get(route('web.home'))
        ->assertOk()
        ->assertSee('Sekali kriuk')
        ->assertSee('Dada Ayam');

    $this->get(route('web.menu', ['q' => 'bakso']))
        ->assertOk()
        ->assertSee('Bakso Goreng Stick')
        ->assertDontSee('Dada Ayam');
});

it('adds a product to the shared cart and opens checkout', function () {
    createWebProducts();

    $this->from(route('web.menu'))
        ->post(route('web.cart.add'), ['product_id' => 'dada-ayam'])
        ->assertRedirect(route('web.menu'))
        ->assertSessionHas('cart', ['dada-ayam' => 1]);

    $this->withSession(['cart' => ['dada-ayam' => 2]])
        ->get(route('web.checkout'))
        ->assertOk()
        ->assertSee('Selesaikan pesanan')
        ->assertSee('Rp14.000');
});

it('creates a web order and redirects to its payment page', function () {
    createWebProducts();
    $checkoutToken = (string) Str::uuid();

    $response = $this->withSession(['customer_checkout_token' => $checkoutToken])->post(route('web.checkout.store'), [
        'customer_name' => 'Rifki',
        'phone' => '081234567890',
        'payment_method' => 'cod',
        'checkout_token' => $checkoutToken,
        'items' => ['dada-ayam' => 2],
    ]);

    $order = Order::query()->sole();
    $response->assertRedirect(route('web.payment', $order->id));
    $response->assertSessionHas('mobile_order_codes', fn (array $codes): bool => in_array($order->id, $codes, true));
    expect($order->total)->toBe(14000)
        ->and($order->stock_reserved)->toBeTrue()
        ->and(Product::query()->findOrFail('dada-ayam')->stock)->toBe(48);
});

it('does not process the same checkout token twice', function () {
    createWebProducts();
    $checkoutToken = (string) Str::uuid();
    $payload = [
        'customer_name' => 'Rifki',
        'phone' => '081234567890',
        'payment_method' => 'cod',
        'checkout_token' => $checkoutToken,
        'items' => ['dada-ayam' => 1],
    ];

    $this->withSession(['customer_checkout_token' => $checkoutToken])
        ->post(route('web.checkout.store'), $payload)
        ->assertRedirect();

    $this->withSession(['customer_checkout_token' => $checkoutToken])
        ->from(route('web.checkout'))
        ->post(route('web.checkout.store'), $payload)
        ->assertRedirect(route('web.checkout'))
        ->assertSessionHasErrors('checkout_token');

    expect(Order::query()->count())->toBe(1)
        ->and(Product::query()->findOrFail('dada-ayam')->stock)->toBe(49);
});

it('rejects a second order after the remaining stock has been reserved', function () {
    createWebProducts();
    Product::query()->whereKey('dada-ayam')->update(['stock' => 1]);

    foreach (['Pembeli Satu', 'Pembeli Dua'] as $index => $customerName) {
        $checkoutToken = (string) Str::uuid();
        $response = $this->withSession(['customer_checkout_token' => $checkoutToken])
            ->from(route('web.checkout'))
            ->post(route('web.checkout.store'), [
                'customer_name' => $customerName,
                'phone' => '081234567890',
                'payment_method' => 'cod',
                'checkout_token' => $checkoutToken,
                'items' => ['dada-ayam' => 1],
            ]);

        if ($index === 0) {
            $response->assertRedirect();
        } else {
            $response->assertRedirect(route('web.checkout'))->assertSessionHasErrors('cart');
        }
    }

    expect(Order::query()->count())->toBe(1)
        ->and(Product::query()->findOrFail('dada-ayam')->stock)->toBe(0);
});

it('does not decrement reserved stock again when admin confirms the order', function () {
    createWebProducts();
    $checkoutToken = (string) Str::uuid();

    $this->withSession(['customer_checkout_token' => $checkoutToken])->post(route('web.checkout.store'), [
        'customer_name' => 'Rifki',
        'phone' => '081234567890',
        'payment_method' => 'cod',
        'checkout_token' => $checkoutToken,
        'items' => ['dada-ayam' => 2],
    ]);

    $order = Order::query()->sole();
    $this->withSession(['admin' => 'admin'])
        ->post("/admin/orders/{$order->id}/confirm")
        ->assertRedirect();

    expect($order->fresh()->status)->toBe('confirmed')
        ->and($order->fresh()->stock_reserved)->toBeTrue()
        ->and(Product::query()->findOrFail('dada-ayam')->stock)->toBe(48)
        ->and(Transaction::query()->count())->toBe(1);
});

it('keeps web order details private to the current device session', function () {
    createWebProducts();
    Order::query()->create([
        'id' => 'AYM-3333',
        'customer_name' => 'Pelanggan Lain',
        'phone' => '081234567890',
        'items' => json_encode([]),
        'total' => 7000,
        'note' => '',
        'payment_method' => 'cod',
        'status' => 'pending',
    ]);

    $this->get(route('web.orders.show', 'AYM-3333'))->assertNotFound();
    $this->get(route('web.payment', 'AYM-3333'))->assertNotFound();
});
