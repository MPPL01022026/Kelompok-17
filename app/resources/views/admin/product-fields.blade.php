<label>Nama produk<input name="name" value="{{ old('name', $product->name ?? '') }}" required></label>
<label>Kategori<input name="category" value="{{ old('category', $product->category ?? 'Ayam Goreng') }}" required></label>
<label>Harga<input name="price" type="number" min="0" value="{{ old('price', $product->price ?? '') }}" required></label>
<label>Stok<input name="stock" type="number" min="0" value="{{ old('stock', $product->stock ?? '') }}" required></label>
<label>Deskripsi<textarea name="description" required>{{ old('description', $product->description ?? '') }}</textarea></label>
<label>Foto produk<input name="image" type="file" accept="image/*"></label>
<label>URL gambar<input name="image_url" value="{{ old('image_url', $product->image_url ?? '') }}" placeholder="/img/nama-file.jpg"></label>
