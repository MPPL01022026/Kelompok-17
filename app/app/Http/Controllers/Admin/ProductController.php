<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request, ?string $id = null): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:500'],
        ]);

        $product = $id ? Product::find($id) : null;
        abort_if($id && !$product, 404);

        $imageUrl = $product->image_url ?? ($data['image_url'] ?? '');
        if ($request->hasFile('image')) {
            $imageUrl = '/storage/' . $request->file('image')->store('products', 'public');
        }

        $attributes = [
            'name' => $data['name'],
            'category' => $data['category'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'description' => $data['description'],
            'image_url' => $imageUrl,
            'active' => true,
        ];

        if ($product) {
            $product->update($attributes);
        } else {
            Product::create($attributes + ['id' => (string) Str::uuid()]);
        }

        return redirect('/admin?tab=products')->with('status', 'Produk disimpan.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['active' => false]);

        return back()->with('status', 'Produk diarsipkan.');
    }
}
