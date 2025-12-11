<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $product->name }}</title>
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

        <div class="flex items-center gap-2">
          <button onclick="history.back()" class="inline-flex items-center gap-2 text-white rounded-full px-3 py-1 ring-1 ring-white/50 hover:bg-white/10 transition-transform active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            <span>Kembali</span>
          </button>

          <a href="{{ route('store.cart') }}" class="text-white active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
              <path d="M7.5 3.75A2.25 2.25 0 0 0 5.25 6v.75H3a.75.75 0 0 0 0 1.5h1.5l1.05 8.4A3 3 0 0 0 8.52 19.5h7.29a3 3 0 0 0 2.97-2.58l1.14-8.58H20.25a.75.75 0 0 0 0-1.5h-2.25V6a2.25 2.25 0 0 0-2.25-2.25h-8.25Z" />
              <circle cx="9" cy="21" r="1.5" />
              <circle cx="16" cy="21" r="1.5" />
            </svg>
          </a>
        </div>
      </div>
      <div class="h-1 bg-sky-400"></div>
    </header>

    <main class="max-w-md mx-auto px-4 py-6 space-y-4">
      <section class="bg-red-700 text-white rounded-2xl p-4 space-y-4">

        <div class="bg-gray-300 rounded-xl h-44 flex items-center justify-center overflow-hidden">
          @if($product->image_url)
            <img src="{{ asset($product->image_url) }}" class="object-cover w-full h-full">
          @else
            <div class="text-slate-700">Tidak ada gambar</div>
          @endif
        </div>

        <div class="space-y-1">
          <div class="text-lg font-semibold">{{ $product->name }}</div>
          <div class="text-white font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
          <div class="text-sm">Stok: {{ $product->stock }}</div>
        </div>
      </section>

      <section class="bg-gray-300 rounded-2xl p-4">
        <div class="text-slate-800 font-medium">Deskripsi & Manfaat</div>
        <p class="mt-2 text-slate-700 text-sm">
          {{ $product->description ?? 'Tidak ada deskripsi.' }}
        </p>
      </section>

      <div class="pt-2 grid grid-cols-2 gap-3">

        {{-- BUY NOW BUTTON --}}
        <form id="buyNowForm" class="w-full">
    @csrf
    <input type="hidden" name="amount" value="{{ $product->price }}">
    <input type="hidden" name="name" value="{{ $product->name }}">
    <input type="hidden" name="qty" value="1">

    <button type="submit"
      class="w-full rounded-full bg-red-600 text-white py-3 text-center font-medium active:scale-95 hover:shadow-md">
      Beli
    </button>
</form>



        {{-- ADD TO CART BUTTON --}}
        <button id="addCartBtn"
          data-name="{{ $product->name }}"
          data-price="{{ $product->price }}"
          class="w-full rounded-full bg-slate-200 text-slate-800 py-3 font-medium active:scale-95 hover:shadow-md">
          Masukan ke Keranjang
        </button>
      </div>
    </main>

    <script>
      const btn = document.getElementById('addCartBtn');

      const read = () => {
        try { return JSON.parse(localStorage.getItem('cart') || '[]') }
        catch { return [] }
      }
      const write = (items) => localStorage.setItem('cart', JSON.stringify(items))

      btn?.addEventListener('click', () => {
        const name = btn.dataset.name
        const price = Number(btn.dataset.price)

        const arr = read()
        const idx = arr.findIndex(i => i.name === name)

        if (idx >= 0) arr[idx].qty += 1
        else arr.push({ name, price, qty: 1 })

        write(arr)

        window.location.href = "{{ route('store.cart') }}"
      });
    </script>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
document.getElementById('buyNowForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);

    try {
        const res = await fetch("{{ route('payment.create') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value },
            body: data
        });

        const json = await res.json();

        if (json.snap_token) {
            const orderId = json.order_id;
            snap.pay(json.snap_token, {
                onSuccess: function(result) {
                    window.location.href = `/payment/result?order_id=${orderId}`;
                },
                onPending: function(result) {
                    window.location.href = `/payment/result?order_id=${orderId}`;
                },
                onError: function(result) {
                    alert("Terjadi kesalahan saat pembayaran. Silakan coba lagi.");
                },
                onClose: function() {
                    alert("Pembayaran dibatalkan.");
                }
            });
        } else {
            alert("Gagal mendapatkan token pembayaran.");
        }
    } catch (err) {
        console.error(err);
        alert("Terjadi kesalahan jaringan. Silakan coba lagi.");
    }
});
</script>


  </body>
</html>
