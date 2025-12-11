<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Input Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-white text-slate-800">
    <header class="bg-red-600">
      <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center">
            <span class="text-red-600 font-semibold">AP</span>
          </div>
          
        </div>
        <a href="admin.html" class="inline-flex items-center gap-2 text-white rounded-full px-3 py-1 ring-1 ring-white/50 hover:bg-white/10 transition-transform active:scale-95">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
          <span>Kembali</span>
        </a>
      </div>
      <div class="h-1 bg-sky-400"></div>
    </header>

    <main class="max-w-md mx-auto px-4 py-6">
      <div class="mb-4">
        <div id="preview" class="bg-gray-100 rounded-xl h-40 flex items-center justify-center text-slate-600">Preview Gambar</div>
      </div>
      <form id="inputForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Nama Produk</label>
          <input type="text" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2 focus:outline-none" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700">Harga</label>
            <input type="number" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2 focus:outline-none" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700">Stok</label>
            <input type="number" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2 focus:outline-none" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Kategori</label>
          <input type="text" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2 focus:outline-none" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">URL Gambar</label>
          <input id="imageUrl" type="url" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2 focus:outline-none" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Upload Gambar</label>
          <input id="imageFile" type="file" accept="image/*" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2 focus:outline-none" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
          <textarea rows="4" class="mt-1 w-full rounded-lg bg-gray-100 px-3 py-2 focus:outline-none"></textarea>
        </div>
        <button type="submit" class="w-full rounded-full bg-red-600 text-white py-3 font-medium transition-transform duration-200 active:scale-95 hover:shadow-md">Simpan</button>
      </form>
    </main>
    <script>
      const imageUrl = document.getElementById('imageUrl')
      const imageFile = document.getElementById('imageFile')
      const preview = document.getElementById('preview')
      const updatePreview = (url) => {
        if (!url) { preview.innerHTML = 'Preview Gambar'; return }
        preview.innerHTML = `<img src="${url}" alt="produk" class="h-full w-full object-cover rounded-xl"/>`
      }
      imageUrl?.addEventListener('input', (e) => updatePreview(e.target.value))
      imageFile?.addEventListener('change', (e) => {
        const f = e.target.files?.[0]
        if (!f) return
        const r = new FileReader()
        r.onload = () => updatePreview(r.result)
        r.readAsDataURL(f)
      })
    </script>
  </body>
  </html>
