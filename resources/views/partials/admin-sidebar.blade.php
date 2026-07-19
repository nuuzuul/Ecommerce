<aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 w-72 transform bg-stone-950 p-5 text-stone-300 transition lg:static lg:translate-x-0">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-white">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-amber-500 text-xl">🍪</span>
            <span><b class="block text-lg">Kanrejawataa</b><small class="text-stone-400">Administrator</small></span>
        </a>
        <button @click="sidebar = false" class="lg:hidden">✕</button>
    </div>
    <nav class="mt-10 grid gap-2">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'admin-nav-link-active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'admin-nav-link-active' : '' }}">Kategori</a>
        <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'admin-nav-link-active' : '' }}">Produk</a>
        <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'admin-nav-link-active' : '' }}">Pesanan</a>
        <a href="{{ route('profile.edit') }}" class="admin-nav-link">Profil akun</a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="absolute bottom-6 left-5 right-5">@csrf<button class="w-full rounded-xl border border-stone-700 px-4 py-3 text-left text-sm font-semibold hover:bg-stone-800">Keluar</button></form>
</aside>
