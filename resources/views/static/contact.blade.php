@extends('layouts.store')
@section('title','Kontak Kanrejawataa')
@section('content')
<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8"><div class="grid gap-6 md:grid-cols-2"><div class="rounded-3xl bg-stone-950 p-8 text-white"><p class="text-sm font-black uppercase tracking-[.2em] text-amber-400">Hubungi kami</p><h1 class="mt-3 text-4xl font-black">Ada yang ingin ditanyakan?</h1><p class="mt-4 leading-7 text-stone-300">Tim Kanrejawataa siap membantu informasi produk, pesanan acara, dan pengantaran.</p></div><div class="rounded-3xl bg-white p-8 shadow-sm"><div class="space-y-5"><div><p class="text-sm text-stone-500">WhatsApp</p><p class="font-black">08xx-xxxx-xxxx</p></div><div><p class="text-sm text-stone-500">Instagram</p><p class="font-black">@kanrejawataa</p></div><div><p class="text-sm text-stone-500">Lokasi pengambilan</p><p class="font-black">{{ config('kanrejawataa.pickup_address') }}</p></div></div></div></div></section>
@endsection
