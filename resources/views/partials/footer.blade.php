<footer class="mt-16 bg-stone-950 text-stone-300">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
        <div>
            <h2 class="text-2xl font-black text-white">Kanrejawataa</h2>
            <p class="mt-3 max-w-sm text-sm leading-6 text-stone-400">Aneka kue kering dan kue tradisional Makassar untuk keluarga, hadiah, dan momen spesial.</p>
        </div>
        <div>
            <h3 class="font-bold text-white">Navigasi</h3>
            <div class="mt-3 grid gap-2 text-sm">
                <a href="{{ route('products.index') }}" class="hover:text-amber-400">Katalog produk</a>
                <a href="{{ route('about') }}" class="hover:text-amber-400">Tentang kami</a>
                <a href="{{ route('contact') }}" class="hover:text-amber-400">Kontak</a>
            </div>
        </div>
        <div>
            <h3 class="font-bold text-white">Ikuti kami</h3>
            <p class="mt-3 text-sm text-stone-400">Instagram: kanrejawataa<br>WhatsApp: 082189641051<br>Email: kanrejawataa@gmail.com<br> Alamat: Jalan Toddopuli</p>
        </div>
    </div>
    <div class="border-t border-stone-800 py-5 text-center text-xs text-stone-500">© {{ date('Y') }} Kanrejawataa.</div>
</footer>
