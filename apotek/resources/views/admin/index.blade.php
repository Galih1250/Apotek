<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menu Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-sky-50 text-slate-800">
    <header class="sticky top-0 z-40">
      <div class="bg-red-600">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center">
              <span class="text-red-600 font-semibold">AP</span>
            </div>
            
          </div>
          <div class="relative">
            <button id="adminMenuBtn" class="text-white transition-transform active:scale-95">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
              </svg>
            </button>
            <div id="adminMenuPopover" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-2xl shadow-lg ring-1 ring-slate-200 p-3">
              <a href="{{ route('admin.input') }}" class="block">
                <div class="w-full bg-red-600 text-white rounded-lg px-4 py-2 text-sm text-center transition-transform duration-200 active:scale-95 hover:shadow-md">Input</div>
              </a>
              <button id="logoutBtn" class="mt-3 w-full bg-red-600 text-white rounded-lg px-4 py-2 text-sm text-center transition-transform duration-200 active:scale-95 hover:shadow-md">Log Out</button>
            </div>
          </div>
        </div>
      </div>
      <div class="h-1 bg-sky-400"></div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6">
      <section>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
@foreach($products as $product)
<div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 p-4 flex flex-col h-full">
    <div class="aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-100 flex items-center justify-center">
        @if($product->image_url)
    <img
        src="{{ str_starts_with($product->image_url, 'http')
                ? $product->image_url
                : asset('storage/'.$product->image_url) }}"
        alt="{{ $product->name }}"
        class="h-full w-full object-cover">
@else
    <span class="text-slate-500">No image</span>
@endif



    </div>
    <div class="mt-3 text-sky-600 text-sm font-medium">{{ $product->category->name ?? 'Uncategorized' }}</div>
    <h3 class="mt-1 font-semibold">{{ $product->name }}</h3>
    <div class="mt-2 mb-4 text-slate-900 font-extrabold">RP {{ number_format($product->price,0,',','.') }}</div>
    <div class="mt-auto flex gap-2">
    {{-- EDIT --}}
    <a href="{{ route('admin.edit', $product->id) }}"
       class="flex-1 flex items-center justify-center gap-2 bg-blue-600 text-white rounded-full py-2 transition-transform duration-200 active:scale-95 hover:shadow-md">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             fill="currentColor" class="w-5 h-5">
            <path d="M16.862 3.487a1.74 1.74 0 0 1 2.46 2.46l-9.354 9.354a3.481 3.481 0 0 1-1.534.878l-3.052.813a.75.75 0 0 1-.913-.913l.813-3.052a3.481 3.481 0 0 1 .878-1.534l9.354-9.354Z" />
            <path d="M5.25 19.5h13.5" />
        </svg>
        Edit
    </a>

    {{-- DELETE --}}
    <form action="{{ route('admin.destroy', $product->id) }}"
          method="POST"
          onsubmit="return confirm('Yakin mau hapus produk ini?');"
          class="flex-1">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-red-600 text-white rounded-full py-2 transition-transform duration-200 active:scale-95 hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                 fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd"
                      d="M9.75 3a.75.75 0 0 0-.75.75V4.5H5.25a.75.75 0 0 0 0 1.5h.375l.817 12.26A2.25 2.25 0 0 0 8.688 20.5h6.624a2.25 2.25 0 0 0 2.246-2.24L18.375 6h.375a.75.75 0 0 0 0-1.5H15V3.75a.75.75 0 0 0-.75-.75h-4.5Zm2.25 4.5a.75.75 0 0 0-.75.75v8.25a.75.75 0 0 0 1.5 0V8.25a.75.75 0 0 0-.75-.75Z"
                      clip-rule="evenodd" />
            </svg>
            Hapus
        </button>
    </form>
</div>

</div>
@endforeach
</div>
        </div>
      </section>
    </main>
    <script>
      const adminMenuBtn = document.getElementById('adminMenuBtn')
      const adminMenuPopover = document.getElementById('adminMenuPopover')
      const logoutBtn = document.getElementById('logoutBtn')
      adminMenuBtn?.addEventListener('click', (e) => {
        e.stopPropagation()
        adminMenuPopover?.classList.toggle('hidden')
      })
      document.addEventListener('click', (e) => {
        if (adminMenuPopover && !adminMenuPopover.classList.contains('hidden')) {
          const within = adminMenuPopover.contains(e.target) || adminMenuBtn.contains(e.target)
          if (!within) adminMenuPopover.classList.add('hidden')
        }
      })
      logoutBtn?.addEventListener('click', () => {
        const indexUrl = "{{ route('store.index') }}"
        window.location.href = indexUrl
      })
    </script>
  </body>
  </html>
