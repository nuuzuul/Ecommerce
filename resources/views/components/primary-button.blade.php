<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-black text-stone-950 transition hover:bg-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-200 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
