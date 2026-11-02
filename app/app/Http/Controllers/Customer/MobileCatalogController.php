<?php

namespace App\Http\Controllers\Customer;

use App\Models\Product;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileCatalogController extends MobileStorefrontController
{
    public function home(Request $request): View
    {
        $products = $this->activeProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->cartData($request, $products);
        $settings = Settings::getMap();
        $favoriteIds = ['dada-ayam', 'paha-atas', 'sayap-ayam', 'paha-bawah', 'ceker-ayam', 'bakso-goreng-stick'];
        $favorites = $products->whereIn('id', $favoriteIds)->values();

        if ($favorites->isEmpty()) {
            $favorites = $products->take(6);
        }

        return view('customer.mobile.home', compact(
            'products', 'favorites', 'cart', 'cartProducts', 'cartCount', 'cartTotal', 'settings',
        ));
    }

    public function menu(Request $request): View
    {
        $allProducts = $this->activeProducts();
        [$cart, $cartProducts, $cartCount, $cartTotal] = $this->cartData($request, $allProducts);
        $search = $request->string('q')->trim()->toString();
        $products = $allProducts;

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $products = $allProducts
                ->filter(fn (Product $product): bool => str_contains(mb_strtolower($product->name), $needle)
                    || str_contains(mb_strtolower($product->description), $needle)
                    || str_contains(mb_strtolower($product->category), $needle))
                ->values();
        }

        return view('customer.mobile.menu.shop-menu', compact(
            'products', 'search', 'cart', 'cartProducts', 'cartCount', 'cartTotal',
        ));
    }

    public function splash(): View
    {
        return view('customer.mobile.splash');
    }
}
