<aside class="h-fit rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
    <div class="border-b border-stone-100 pb-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">
            Akun pembeli
        </p>

        <h2 class="mt-1 font-bold text-stone-900">
            {{ auth()->user()->name }}
        </h2>

        <p class="truncate text-sm text-stone-500">
            {{ auth()->user()->email }}
        </p>
    </div>

    <nav class="mt-4 grid gap-1">
        <a
            href="{{ route('account.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('account.dashboard')
                ? 'sidebar-link-active'
                : '' }}"
        >
            Ringkasan akun
        </a>

        <a
            href="{{ route('profile.edit') }}"
            class="sidebar-link {{ request()->routeIs('profile.*')
                ? 'sidebar-link-active'
                : '' }}"
        >
            Profil & password
        </a>

        <a
            href="{{ route('cart.index') }}"
            class="sidebar-link {{ request()->routeIs('cart.*', 'checkout.*')
                ? 'sidebar-link-active'
                : '' }}"
        >
            Keranjang
        </a>

        <a
            href="{{ route('orders.index') }}"
            class="sidebar-link {{ request()->routeIs('orders.*')
                ? 'sidebar-link-active'
                : '' }}"
        >
            Pesanan saya
        </a>

        <div x-data="{ openLogout: false }">
            <button
                type="button"
                x-on:click="openLogout = true"
                class="sidebar-link w-full text-left text-red-600"
            >
                Keluar
            </button>

            <template x-teleport="body">
                <div
                    x-cloak
                    x-show="openLogout"
                    x-transition.opacity
                    x-on:keydown.escape.window="openLogout = false"
                    class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
                >
                    <div
                        class="absolute inset-0 bg-stone-950/70 backdrop-blur-sm"
                        x-on:click="openLogout = false"
                    ></div>

                    <div
                        x-show="openLogout"
                        x-transition
                        x-on:click.stop
                        class="relative z-10 w-full max-w-md rounded-3xl bg-white p-6 text-stone-800 shadow-2xl"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                <x-icon name="logout" />
                            </div>

                            <div>
                                <h2 class="text-xl font-black text-stone-900">
                                    Keluar dari akun?
                                </h2>

                                <p class="mt-2 text-sm leading-6 text-stone-600">
                                    Kamu akan keluar dari akun pembeli Kanrejawataa.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button
                                type="button"
                                x-on:click="openLogout = false"
                                class="rounded-xl border border-stone-300 px-4 py-2.5 font-bold text-stone-700 transition hover:bg-stone-100"
                            >
                                Batal
                            </button>

                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="rounded-xl bg-red-600 px-4 py-2.5 font-bold text-white transition hover:bg-red-700"
                                >
                                    Ya, keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </nav>
</aside>