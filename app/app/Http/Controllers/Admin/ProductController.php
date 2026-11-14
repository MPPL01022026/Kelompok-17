<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:500'],
        ]);

        $product = $id ? Product::find($id) : null;
        abort_if($id && ! $product, 404);

        $submittedUrl = trim((string) ($data['image_url'] ?? ''));
        $defaultImg = str_contains(mb_strtolower($data['category']), 'camilan') ? '/img/cat-bakso.png' : '/img/cat-dada.png';
        $existingUrl = $product?->image_url ?? '';
        if ($existingUrl !== '' && str_ends_with($existingUrl, '.svg')) {
            $existingUrl = '';
        }

        $imageUrl = $submittedUrl !== '' ? $submittedUrl : ($existingUrl !== '' ? $existingUrl : $defaultImg);
        if ($request->hasFile('image')) {
            $uploadDir = public_path('img/uploads');
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file = $request->file('image');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $slug = Str::slug($data['name']) ?: 'produk';
            $filename = 'prod-' . $slug . '-' . time() . '-' . Str::lower(Str::random(5)) . '.' . $ext;
            $file->move($uploadDir, $filename);
            $imageUrl = '/img/uploads/' . $filename;
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

    public function restore(string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['active' => true]);

        return redirect('/admin?tab=products')->with('status', 'Produk dipulihkan ke katalog.');
    }

    public function adjustStock(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'delta' => ['nullable', 'integer', 'min:-500', 'max:500'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        $productName = DB::transaction(function () use ($request, $data, $id): string {
            $product = Product::query()->where('id', $id)->lockForUpdate()->first();
            abort_unless($product, 404);

            $nextStock = $request->filled('stock')
                ? (int) $data['stock']
                : max(0, $product->stock + (int) ($data['delta'] ?? 0));

            $product->update(['stock' => $nextStock]);

            return (string) $product->name;
        });

        return redirect('/admin?tab=products')->with('status', "Stok {$productName} diperbarui.");
    }
}
