<nav
    x-data="{ open: false }"
    class="sticky top-0 z-40 border-b border-amber-100 bg-white/95 shadow-sm backdrop-blur"
>
    <div class="mx-auto flex min-h-20 max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
            <img
                src="{{ asset('images/branding/logo.svg') }}"
                alt="Logo Kanrejawataa"
                class="h-11 w-11 shrink-0 rounded-2xl object-contain shadow-sm"
            >
            <span class="min-w-0">
                <span class="block truncate text-xl font-black tracking-tight text-stone-900">
                    Kanrejawataa
                </span>
                <span class="block truncate text-[10px] font-semibold uppercase tracking-[.18em] text-amber-700">
                    Kue khas Makassar
                </span>
            </span>
        </a>

        <div class="hidden items-center gap-1 md:flex">
            <a
                href="{{ route('home') }}"
                class="nav-store-link {{ request()->routeIs('home') ? 'nav-store-link-active' : '' }}"
            >
                Beranda
            </a>

            <div class="group relative">
                <a
                    href="{{ route('products.index') }}"
                    class="nav-store-link {{ request()->routeIs('products.*', 'categories.*') ? 'nav-store-link-active' : '' }}"
                >
                    Produk
                </a>

                <div class="invisible absolute left-0 top-full z-50 mt-2 w-64 translate-y-2 rounded-2xl border border-amber-100 bg-white p-2 opacity-0 shadow-xl transition group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                    <a
                        href="{{ route('products.index') }}"
                        class="block rounded-xl px-3 py-2 text-sm font-bold hover:bg-amber-50"
                    >
                        Semua produk
                    </a>

                    @foreach ($navCategories as $navCategory)
                        <a
                            href="{{ route('categories.show', $navCategory) }}"
                            class="block rounded-xl px-3 py-2 text-sm font-semibold text-stone-600 hover:bg-amber-50 hover:text-amber-800"
                        >
                            {{ $navCategory->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <a
                href="{{ route('about') }}"
                class="nav-store-link {{ request()->routeIs('about') ? 'nav-store-link-active' : '' }}"
            >
                Tentang
            </a>

            <a
                href="{{ route('contact') }}"
                class="nav-store-link {{ request()->routeIs('contact') ? 'nav-store-link-active' : '' }}"
            >
                Kontak
            </a>
        </div>

        <div class="hidden items-center gap-2 md:flex">
            <form
                method="GET"
                action="{{ route('products.index') }}"
                class="hidden items-center lg:flex"
            >
                <label class="sr-only" for="nav-search">Cari produk</label>
                <input
                    id="nav-search"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-40 rounded-xl border-stone-200 py-2 text-sm focus:border-amber-500 focus:ring-amber-200 xl:w-52"
                    placeholder="Cari kue..."
                >
            </form>

            @auth
                @if (auth()->user()->isBuyer())
                    <a
                        href="{{ route('cart.index') }}"
                        class="relative rounded-xl border border-amber-200 px-3 py-2 text-sm font-semibold text-stone-700 hover:bg-amber-50"
                    >
                        Keranjang
                        @php($cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0)
                        @if ($cartCount > 0)
                            <span class="ml-1 rounded-full bg-amber-500 px-2 py-0.5 text-xs text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @endif

                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-xl bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700"
                >
                    Kelola akun
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="px-3 py-2 text-sm font-semibold text-stone-700"
                >
                    Masuk
                </a>
                <a
                    href="{{ route('register') }}"
                    class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-bold text-stone-950 hover:bg-amber-400"
                >
                    Daftar
                </a>
            @endauth
        </div>

        <button
            type="button"
            @click="open = !open"
            class="rounded-xl border border-stone-200 p-2 md:hidden"
            aria-label="Buka navigasi"
        >
            ☰
        </button>
    </div>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="border-t border-stone-100 bg-white px-4 py-4 md:hidden"
    >
        <form
            method="GET"
            action="{{ route('products.index') }}"
            class="mb-3 flex gap-2"
        >
            <input
                name="search"
                value="{{ request('search') }}"
                class="form-input"
                placeholder="Cari kue..."
            >
            <button class="btn-primary" aria-label="Cari produk">
                <x-icon name="search" />
            </button>
        </form>

        <div class="grid gap-2">
            <a href="{{ route('home') }}" class="mobile-nav-link">Beranda</a>
            <a href="{{ route('products.index') }}" class="mobile-nav-link">
                Semua produk
            </a>

            @foreach ($navCategories as $navCategory)
                <a
                    href="{{ route('categories.show', $navCategory) }}"
                    class="mobile-nav-link pl-6 text-stone-500"
                >
                    — {{ $navCategory->name }}
                </a>
            @endforeach

            <a href="{{ route('about') }}" class="mobile-nav-link">Tentang</a>
            <a href="{{ route('contact') }}" class="mobile-nav-link">Kontak</a>

            @auth
                @if (auth()->user()->isBuyer())
                    <a href="{{ route('cart.index') }}" class="mobile-nav-link">
                        Keranjang
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="mobile-nav-link">
                    Kelola akun
                </a>
            @else
                <a href="{{ route('login') }}" class="mobile-nav-link">Masuk</a>
                <a href="{{ route('register') }}" class="mobile-nav-link">Daftar</a>
            @endauth
        </div>
    </div>
</nav>
