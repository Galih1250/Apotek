<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Keranjang</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Midtrans -->
    <script src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</head>

<body class="bg-white text-slate-800">
<header class="bg-red-600">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center">
            <span class="text-red-600 font-semibold">AP</span>
        </div>
        <button onclick="history.back()" class="inline-flex items-center gap-2 text-white rounded-full px-3 py-1 ring-1 ring-white/50 hover:bg-white/10 transition-transform active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
            <span>Kembali</span>
        </button>
    </div>
    <div class="h-1 bg-sky-400"></div>
</header>

<main class="max-w-md mx-auto px-4 py-6 space-y-4">
    <section class="rounded-2xl bg-slate-50 p-4">
        <div id="empty" class="text-slate-600">Keranjang kosong</div>
        <div id="list" class="space-y-3"></div>
        <div id="total" class="mt-4 flex justify-between font-semibold"></div>

        <div class="mt-4 grid grid-cols-3 gap-3">
            <button id="clearBtn" class="rounded-full bg-slate-200 text-slate-800 py-3 transition-transform active:scale-95 hover:shadow">Kosongkan</button>

            <a href="{{ route('store.index') }}" class="rounded-full bg-slate-200 text-slate-800 py-3 text-center transition-transform active:scale-95 hover:shadow">
                Belanja Lagi
            </a>

           <button 
    id="payAllBtn" 
    class="rounded-full bg-red-600 text-white py-3 transition-transform active:scale-95 hover:shadow">
    Bayar Semua
</button>

        </div>
    </section>
</main>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" 
    data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script>
document.getElementById('payAllBtn').addEventListener('click', function () {
    
    const btn = document.getElementById('payAllBtn');
    btn.disabled = true;
    btn.textContent = 'Processing...';

    fetch('{{ route("store.pay") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ pay_all: true })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Payment creation failed.');
            btn.disabled = false;
            btn.textContent = 'Bayar Semua';
            return;
        }

        snap.pay(data.snap_token, {
            onSuccess: function (result) {
                window.location.href = '/payment/result?order_id=' + data.order_id;
            },
            onPending: function (result) {
                window.location.href = '/payment/result?order_id=' + data.order_id;
            },
            onError: function (result) {
                alert('Payment failed.');
                btn.disabled = false;
                btn.textContent = 'Bayar Semua';
            },
            onClose: function () {
                btn.disabled = false;
                btn.textContent = 'Bayar Semua';
            }
        });
    })
    .catch(err => {
        console.error(err);
        alert('Server error.');
        btn.disabled = false;
        btn.textContent = 'Bayar Semua';
    });

});
</script>


<script>
const money = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
const read = () => { try { return JSON.parse(localStorage.getItem('cart') || '[]'); } catch { return [] } };
const write = (items) => localStorage.setItem('cart', JSON.stringify(items));

const render = () => {
    const items = read();
    const empty = document.getElementById('empty');
    const list = document.getElementById('list');
    const total = document.getElementById('total');
    const payAll = document.getElementById('payAllBtn');

    if (!items.length) {
        empty.classList.remove('hidden');
        list.innerHTML = '';
        total.innerHTML = '';
        payAll.classList.add('hidden');
        return;
    }

    empty.classList.add('hidden');
    payAll.classList.remove('hidden');

    list.innerHTML = items.map((it, idx) => `
        <div class="flex items-center justify-between rounded-xl bg-white shadow-sm ring-1 ring-slate-200 p-3">
            <div>
                <div class="font-medium">${it.name}</div>
                <div class="text-slate-700 text-sm">${money(it.price)} × ${it.qty}</div>
            </div>

            <div class="flex items-center gap-2">
                <button data-idx="${idx}" data-act="minus" class="h-8 w-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/></svg>
                </button>

                <button data-idx="${idx}" data-act="plus" class="h-8 w-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                </button>

                <button data-idx="${idx}" data-act="remove" class="h-8 w-8 rounded-full bg-red-600 text-white flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12"/><path d="M18 6l-12 12"/></svg>
                </button>
            </div>
        </div>
    `).join('');

    const sum = items.reduce((a, b) => a + (b.price * b.qty), 0);
    total.innerHTML = `<span>Total</span><span>${money(sum)}</span>`;

    // === INSTANT MIDTRANS POPUP ===
    payAll.onclick = () => {
        const now = read();
        if (!now.length) return alert('Keranjang kosong');

        const totalNow = now.reduce((a, b) => a + (b.price * b.qty), 0);

        fetch("{{ route('payment.create') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                amount: totalNow,
                description: "Pembayaran keranjang"
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert("Gagal membuat transaksi");
                return;
            }

            snap.pay(data.snap_token, {
                onSuccess: function(result){
                    window.location.href = "{{ route('payment.result') }}?order_id=" + data.order_id;
                },
                onPending: function(result){
                    window.location.href = "{{ route('payment.result') }}?order_id=" + data.order_id;
                },
                onError: function(result){
                    alert("Terjadi error saat pembayaran");
                }
            });
        })
        .catch(err => {
            console.error(err);
            alert("Server error");
        });
    };

    list.querySelectorAll('button[data-act]').forEach(btn => {
        btn.addEventListener('click', () => {
            const act = btn.dataset.act;
            const idx = Number(btn.dataset.idx);
            const arr = read();
            const it = arr[idx];

            if (!it) return;

            if (act === 'minus') it.qty = Math.max(1, it.qty - 1);
            else if (act === 'plus') it.qty += 1;
            else if (act === 'remove') arr.splice(idx, 1);

            write(arr);
            render();
        });
    });
};

document.getElementById('clearBtn').addEventListener('click', () => { write([]); render(); });
render();
</script>

</body>
</html>
