@props(['value'])
<label {{ $attributes->merge(['class' => 'block text-sm font-bold text-stone-700']) }}>{{ $value ?? $slot }}</label>
