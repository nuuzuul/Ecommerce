@props([
    'type' => 'success',
    'dismissible' => true,
])

@php
    $styles = [
        'success' => [
            'wrapper' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'icon' => 'text-emerald-600',
            'button' => 'text-emerald-700 hover:bg-emerald-100',
        ],
        'error' => [
            'wrapper' => 'border-red-200 bg-red-50 text-red-800',
            'icon' => 'text-red-600',
            'button' => 'text-red-700 hover:bg-red-100',
        ],
        'warning' => [
            'wrapper' => 'border-amber-200 bg-amber-50 text-amber-800',
            'icon' => 'text-amber-600',
            'button' => 'text-amber-700 hover:bg-amber-100',
        ],
        'info' => [
            'wrapper' => 'border-blue-200 bg-blue-50 text-blue-800',
            'icon' => 'text-blue-600',
            'button' => 'text-blue-700 hover:bg-blue-100',
        ],
    ];

    $style = $styles[$type] ?? $styles['success'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition.opacity.duration.200ms
    {{ $attributes->merge([
        'class' => "flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm shadow-sm {$style['wrapper']}"
    ]) }}
>
    <div class="mt-0.5 shrink-0 {{ $style['icon'] }}">
        @if ($type === 'success')
            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />
            </svg>
        @elseif ($type === 'error')
            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v3.75m0 3.75h.008v.008H12v-.008ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />
            </svg>
        @elseif ($type === 'warning')
            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374L10.05 3.378c.865-1.5 3.03-1.5 3.896 0l7.357 12.748Z"
                />
            </svg>
        @else
            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M11.25 11.25 12 10.5m0 0 .75.75M12 10.5v6.75m9-5.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />
            </svg>
        @endif
    </div>

    <div class="min-w-0 flex-1 leading-6">
        {{ $slot }}
    </div>

    @if ($dismissible)
        <button
            type="button"
            x-on:click="show = false"
            class="shrink-0 rounded-lg p-1 transition {{ $style['button'] }}"
            aria-label="Tutup alert"
        >
            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18 18 6M6 6l12 12"
                />
            </svg>
        </button>
    @endif
</div>