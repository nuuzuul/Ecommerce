@props(['disabled' => false])
<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-stone-300 shadow-sm focus:border-amber-500 focus:ring-amber-200']) }}>
