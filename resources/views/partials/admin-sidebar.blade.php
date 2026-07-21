<aside
    :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 w-72 transform bg-stone-950 p-5 text-stone-300 transition lg:static lg:translate-x-0"
>
    <div class="flex items-center justify-between">
        <a
            href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 text-white"
        >
            <img
                src="{{ asset('images/branding/logo.svg') }}"
                alt="Logo Kanrejawataa"
                class="h-12 w-12 rounded-2xl object-contain"
            >

            <span>
                <b class="block text-lg">
                    Kanrejawataa
                </b>

                <small class="text-stone-400">
                    Administrator
                </small>
            </span>
        </a>

        <button
            type="button"
            @click="sidebar = false"
            class="rounded-lg p-2 transition hover:bg-stone-800 lg:hidden"
            aria-label="Tutup sidebar"
        >
            <x-icon name="close" />
        </button>
    </div>

    <nav class="mt-10 grid gap-2">
        <a
            href="{{ route('admin.dashboard') }}"
            class="admin-nav-link {{ request()->routeIs('admin.dashboard')
                ? 'admin-nav-link-active'
                : '' }}"
        >
            Dashboard
        </a>

        <a
            href="{{ route('admin.categories.index') }}"
            class="admin-nav-link {{ request()->routeIs('admin.categories.*')
                ? 'admin-nav-link-active'
                : '' }}"
        >
            Kategori
        </a>

        <a
            href="{{ route('admin.products.index') }}"
            class="admin-nav-link {{ request()->routeIs('admin.products.*')
                ? 'admin-nav-link-active'
                : '' }}"
        >
            Produk
        </a>

        <a
            href="{{ route('admin.orders.index') }}"
            class="admin-nav-link {{ request()->routeIs('admin.orders.*')
                ? 'admin-nav-link-active'
                : '' }}"
        >
            Pesanan
        </a>

        <a
            href="{{ route('profile.edit') }}"
            class="admin-nav-link {{ request()->routeIs('profile.edit')
                ? 'admin-nav-link-active'
                : '' }}"
        >
            Profil akun
        </a>
    </nav>

    <div
        x-data="{ openLogout: false }"
        class="absolute bottom-6 left-5 right-5"
    >
        <button
            type="button"
            x-on:click="openLogout = true"
            class="flex w-full items-center gap-3 rounded-xl border border-stone-700 px-4 py-3 text-left text-sm font-semibold transition hover:bg-stone-800"
        >
            <x-icon name="logout" />

            <span>Keluar</span>
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
                                Kamu akan keluar dari panel admin Kanrejawataa.
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
</aside>