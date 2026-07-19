@props(['status'])
@php
$styles = [
    'belum_bayar' => 'bg-red-100 text-red-700',
    'menunggu_verifikasi' => 'bg-amber-100 text-amber-800',
    'sudah_bayar' => 'bg-emerald-100 text-emerald-700',
    'diproses' => 'bg-blue-100 text-blue-700',
    'siap_diambil' => 'bg-violet-100 text-violet-700',
    'dikirim' => 'bg-cyan-100 text-cyan-700',
    'selesai' => 'bg-emerald-100 text-emerald-700',
];
$label = ucwords(str_replace('_', ' ', $status));
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-2.5 py-1 text-xs font-bold '.($styles[$status] ?? 'bg-stone-100 text-stone-700')]) }}>{{ $label }}</span>
