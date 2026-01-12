<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Wijaya Medika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-sky-50 text-slate-800">

<header class="sticky top-0 z-40">
    <div class="bg-red-600">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">

            <div class="flex items-center gap-3">
    <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center">
        <span class="text-red-600 font-semibold">AP</span>
    </div>

    @auth
    <span class="text-white text-sm font-medium hidden sm:inline">
        Welcome{{ auth()->user()->is_admin ? ', Admin' : '' }}, {{ auth()->user()->name }}
    </span>
@endauth

</div>


            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="menuBtn" class="flex items-center text-white transition-transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor"
                             class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                    </button>

                    <div id="menuPopover"
                         class="hidden absolute right-0 mt-2 w-44 sm:w-52 bg-white rounded-2xl shadow-lg ring-1 ring-slate-200 p-3">
                        <a href="{{ route('login') }}" class="block">
                            <div class="w-full bg-red-600 text-white rounded-lg px-4 py-2 text-sm text-center transition-transform duration-200 active:scale-95 hover:shadow-md">
                                Login / Signup
                            </div>
                        </a>
                        <a href="{{ route('admin.index') }}" class="block">
                            <div class="w-full bg-red-600 text-white rounded-lg px-4 py-2 text-sm text-center transition-transform duration-200 active:scale-95 hover:shadow-md">
                                Admin Dashboard
                            </div>
                        </a>
                    </div>
                </div>
                <a href="{{ route('store.cart') }}"
                   class="flex items-center text-white transition-transform active:scale-95"
                   aria-label="Keranjang">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 24 24"
                         fill="currentColor" class="w-7 h-7">
                        <path
                            d="M7.5 3.75A2.25 2.25 0 0 0 5.25 6v.75H3a.75.75 0 0 0 0 1.5h1.5l1.05 8.4A3 3 0 0 0 8.52 19.5h7.29a3 3 0 0 0 2.97-2.58l1.14-8.58H20.25a.75.75 0 0 0 0-1.5h-2.25V6a2.25 2.25 0 0 0-2.25-2.25h-8.25Z"/>
                        <circle cx="9" cy="21" r="1.5"/>
                        <circle cx="16" cy="21" r="1.5"/>
                    </svg>
                </a>

            </div>
        </div>
    </div>

    <div class="h-1 bg-sky-400"></div>

    <nav id="mobileMenu" class="md:hidden hidden bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="grid grid-cols-2 gap-3">
                <a href="#" class="px-3 py-2 rounded-md bg-sky-100 text-sky-700">Categories</a>
                <a href="#" class="px-3 py-2 rounded-md bg-sky-100 text-sky-700">Promo</a>
            </div>
        </div>
    </nav>
</header>

<main class="max-w-7xl mx-auto px-4 py-6">
    <section>
        {{-- Search + Categories filter bar --}}
        <form method="GET" action="{{ route('store.index') }}" class="mb-4 flex items-center gap-3">
            <input type="text" name="q" value="{{ request('q') ?? '' }}" placeholder="Cari produk atau kategori..."
                   class="flex-1 rounded-full px-4 py-2 border bg-white" />

            {{-- preserve currently selected category when searching --}}
            <input type="hidden" name="category" value="{{ request('category') }}">

            <button type="submit" class="rounded-full bg-red-600 text-white px-4 py-2">Cari</button>
        </form>

        <div class="mb-4 flex items-center gap-3 overflow-auto">
            <a href="{{ route('store.index') }}" class="px-3 py-1 rounded-full text-sm border transition-colors {{ request('category') ? 'bg-white text-slate-700 border-slate-200' : 'bg-red-600 text-white border-red-600' }}">
                All
            </a>

            @foreach($categories ?? [] as $cat)
                <a href="{{ route('store.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                   class="px-3 py-1 rounded-full text-sm border transition-colors {{ request('category') === $cat->slug ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-700 border-slate-200' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Product grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            @forelse ($products as $product)
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 p-4 flex flex-col h-full">

                    <div class="aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-100 flex items-center justify-center">
                        <img src="{{ $product->image_url ?? 'https://placehold.co/400x300?text=No+Image' }}"
                             alt="{{ $product->name }}"
                             class="h-full w-full object-cover">
                    </div>

                    <div class="mt-3 text-sky-600 text-sm font-medium">
                        {{ $product->category->name ?? 'Uncategorized' }}
                    </div>

                    <h3 class="mt-1 font-semibold">{{ $product->name }}</h3>

                    <div class="mt-2 mb-4 text-slate-900 font-extrabold">
                        RP {{ number_format($product->price, 0, ',', '.') }}
                    </div>

                    <a href="{{ route('store.product.show', $product->slug) }}"
                        class="mt-auto w-full flex items-center justify-center gap-2 bg-red-600 text-white rounded-full py-2 transition-transform duration-200 ease-out active:scale-95 hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="currentColor"
                             class="w-5 h-5">
                            <path
                                d="M7.5 3.75A2.25 2.25 0 0 0 5.25 6v.75H3a.75.75 0 0 0 0 1.5h1.5l1.05 8.4A3 3 0 0 0 8.52 19.5h7.29a3 3 0 0 0 2.97-2.58l1.14-8.58H20.25a.75.75 0 0 0 0-1.5h-2.25V6a2.25 2.25 0 0 0-2.25-2.25h-8.25Z"/>
                        </svg>
                    </a>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow-sm ring-1 ring-slate-200 p-6 text-center">
                    No products found in this category.
                </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </section>
</main>

<script>
    document.getElementById('menuBtn')?.addEventListener('click', e => {
        e.stopPropagation();
        document.getElementById('menuPopover')?.classList.toggle('hidden');
    });

    document.addEventListener('click', e => {
        const pop = document.getElementById('menuPopover');
        const btn = document.getElementById('menuBtn');

        if (pop && !pop.classList.contains('hidden')) {
            if (!pop.contains(e.target) && !btn.contains(e.target)) {
                pop.classList.add('hidden');
            }
        }
    });

    document.querySelectorAll('button[data-url]').forEach(btn => {
        btn.addEventListener('click', () => {
            window.location.href = btn.dataset.url;
        });
    });
</script>

</body>
</html>
