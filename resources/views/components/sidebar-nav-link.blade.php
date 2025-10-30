@props(['href' => '#', 'active' => false])

@php
    $isActive = $active ?? request()->routeIs($attributes->get('route') ?? '');
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'group flex items-center gap-3 px-4 py-2 rounded-md transition-colors text-sm']) }}
    :aria-current="{{ $isActive ? "'page'" : "false" }}"
    title="{{ trim($slot->isEmpty() ? '' : preg_replace('/\s+<.*$/', '', strip_tags($slot->toHtml()))) }}"
>
    {{-- Icon slot (first child) --}}
    <span class="icon w-5 h-5 flex-shrink-0">
        {{ $icon ?? '' }}
    </span>

    {{-- Label --}}
    <span class="label truncate">
        {{ $slot }}
    </span>

    @if($isActive)
        <span class="ml-auto w-1.5 h-6 rounded-r bg-blue-600 hidden group-active:block"></span>
    @endif

    <style>
        /* Active / default styles will be applied inline below in layout using group classes + blade logic */
    </style>
</a>
