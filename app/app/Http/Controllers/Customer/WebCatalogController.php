<?php

namespace App\Http\Controllers\Customer;

use App\Models\Product;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebCatalogController extends MobileStorefrontController
{
    public function home(Request $request): View
    {
        $products = $this->activeProducts();
        [, , $cartCount] = $this->cartData($request, $products);
        $favorites = $products->whereIn('id', ['dada-ayam', 'paha-atas', 'sayap-ayam', 'bakso-goreng-stick'])->values();
        $settings = Settings::getMap();

        return view('customer.web.home', compact('products', 'favorites', 'cartCount', 'settings'));
    }

    public function menu(Request $request): View
    {
        $allProducts = $this->activeProducts();
        [, , $cartCount] = $this->cartData($request, $allProducts);
        $search = $request->string('q')->trim()->toString();
        $category = $request->string('category')->trim()->toString();
        $products = $allProducts;

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $products = $products->filter(fn (Product $product): bool => str_contains(mb_strtolower($product->name), $needle)
                || str_contains(mb_strtolower($product->description), $needle)
                || str_contains(mb_strtolower($product->category), $needle));
        }

        if ($category !== '') {
            $products = $products->where('category', $category);
        }

        $products = $products->values();
        $categories = $allProducts->pluck('category')->filter()->unique()->values();

        return view('customer.web.menu', compact('products', 'categories', 'search', 'category', 'cartCount'));
    }
}
