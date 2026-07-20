@props([
    'name',
    'size' => 'h-5 w-5',
])

@switch($name)
    @case('eye')
        <svg {{ $attributes->merge(['class' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'aria-hidden' => 'true']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
            <circle cx="12" cy="12" r="2.25" />
        </svg>
        @break

    @case('edit')
        <svg {{ $attributes->merge(['class' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'aria-hidden' => 'true']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 0 1 3.182 3.182L8.25 18.463 3.75 19.5l1.037-4.5L16.862 3.487Z" />
            <path stroke-linecap="round" d="m14.75 5.6 3.65 3.65" />
        </svg>
        @break

    @case('trash')
        <svg {{ $attributes->merge(['class' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'aria-hidden' => 'true']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15m-10.5 0V5.25h6V7.5m-8.25 0 .75 12h9l.75-12M10 11v5m4-5v5" />
        </svg>
        @break

    @case('plus')
        <svg {{ $attributes->merge(['class' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'aria-hidden' => 'true']) }}>
            <path stroke-linecap="round" d="M12 5v14M5 12h14" />
        </svg>
        @break

    @case('search')
        <svg {{ $attributes->merge(['class' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'aria-hidden' => 'true']) }}>
            <circle cx="11" cy="11" r="6.5" />
            <path stroke-linecap="round" d="m16 16 4 4" />
        </svg>
        @break

    @case('external-link')
        <svg {{ $attributes->merge(['class' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'aria-hidden' => 'true']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 4h6v6m0-6-9 9M19 13v6H5V5h6" />
        </svg>
        @break

    @case('download')
        <svg {{ $attributes->merge(['class' => $size, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'aria-hidden' => 'true']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 20h14" />
        </svg>
        @break
@endswitch
