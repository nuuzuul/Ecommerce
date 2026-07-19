@props(['type' => 'success'])
@php($classes = $type === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700')
<div {{ $attributes->merge(['class' => "rounded-xl border px-4 py-3 text-sm font-medium {$classes}"]) }}>{{ $slot }}</div>
