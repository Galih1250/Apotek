<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Produk</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-slate-800">

<header class="bg-red-600">
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center">
        <span class="text-red-600 font-semibold">AP</span>
      </div>
    </div>

    <a href="{{ route('admin.index') }}"
       class="inline-flex items-center gap-2 text-white rounded-full px-3 py-1 ring-1 ring-white/50 hover:bg-white/10 active:scale-95">
      ← Kembali
    </a>
  </div>
  <div class="h-1 bg-sky-400"></div>
</header>

<main class="max-w-md mx-auto px-4 py-6">

  <div class="mb-4">
    <div id="preview" class="bg-gray-100 rounded-xl h-40 flex items-center justify-center text-slate-600">
      @if($product->image_url)
        <img src="{{ $product->image_url }}" class="h-full w-full object-cover rounded-xl">
      @else
        Preview Gambar
      @endif
    </div>
  </div>

  <form method="POST"
        action="{{ route('admin.update', $product->id) }}"
        enctype="multipart/form-data"
        class="space-y-4">
    @csrf
    @method('PUT')

    <div>
      <label class="text-sm font-medium">Nama Produk</label>
      <input name="name" type="text"
             value="{{ old('name', $product->name) }}"
             class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2">
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-medium">Harga</label>
        <input name="price" type="number" step="0.01"
               value="{{ old('price', $product->price) }}"
               class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2">
      </div>

      <div>
        <label class="text-sm font-medium">Stok</label>
        <input name="stock" type="number"
               value="{{ old('stock', $product->stock) }}"
               class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2">
      </div>
    </div>

    <div>
      <label class="text-sm font-medium">Kategori</label>
      <select name="category_id" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2">
        <option value="">- Pilih Kategori -</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="space-y-1">
  <label class="text-sm font-medium">URL Gambar</label>
  <input
    id="imageUrl"
    name="image_url"
    type="url"
    value="{{ old('image_url', $product->image_url) }}"
    class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2"
    placeholder="https://example.com/image.jpg">
  <p class="text-xs text-gray-500">
    Diisi jika ingin pakai gambar dari URL
  </p>
</div>

<div class="space-y-1">
  <label class="text-sm font-medium">Upload Gambar</label>
  <input
    id="imageFile"
    name="image_file"
    type="file"
    accept="image/*"
    class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2">
  <p class="text-xs text-gray-500">
    Jika upload, gambar ini akan menggantikan URL
  </p>
</div>


    <div>
      <label class="text-sm font-medium">Deskripsi</label>
      <textarea name="description" rows="4"
                class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2">{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <button type="submit"
              class="rounded-full bg-red-600 text-white py-3 font-medium active:scale-95">
        Simpan
      </button>

      <a href="{{ route('admin.index') }}"
         class="rounded-full bg-slate-200 py-3 text-center font-medium">
        Batal
      </a>
    </div>

  </form>
</main>

<script>
  const preview = document.getElementById('preview')
  document.getElementById('imageUrl')?.addEventListener('input', e => {
    if (!e.target.value) return
    preview.innerHTML = `<img src="${e.target.value}" class="h-full w-full object-cover rounded-xl">`
  })

  document.getElementById('imageFile')?.addEventListener('change', e => {
    const f = e.target.files[0]
    if (!f) return
    const r = new FileReader()
    r.onload = () => preview.innerHTML = `<img src="${r.result}" class="h-full w-full object-cover rounded-xl">`
    r.readAsDataURL(f)
  })
</script>

</body>
</html>
