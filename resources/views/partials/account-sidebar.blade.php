<aside class="h-fit rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
    <div class="border-b border-stone-100 pb-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Akun pembeli</p>
        <h2 class="mt-1 font-bold text-stone-900">{{ auth()->user()->name }}</h2>
        <p class="truncate text-sm text-stone-500">{{ auth()->user()->email }}</p>
    </div>
    <nav class="mt-4 grid gap-1">
        <a href="{{ route('account.dashboard') }}" class="sidebar-link {{ request()->routeIs('account.dashboard') ? 'sidebar-link-active' : '' }}">Ringkasan akun</a>
        <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'sidebar-link-active' : '' }}">Profil & password</a>
        <a href="{{ route('cart.index') }}" class="sidebar-link {{ request()->routeIs('cart.*', 'checkout.*') ? 'sidebar-link-active' : '' }}">Keranjang</a>
        <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.*') ? 'sidebar-link-active' : '' }}">Pesanan saya</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-link w-full text-left text-red-600">Keluar</button></form>
    </nav>
</aside>
