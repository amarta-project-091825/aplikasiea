@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 pt-1 pb-1 border-b-2 border-[#ffb800] bg-[#ffb800]/10 text-sm font-medium leading-5 text-[#7f1d1d] dark:text-[#ffb800] focus:outline-none focus:border-[#ffb800] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 pt-1 pb-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-[#ffb800] dark:hover:text-[#ffb800] hover:border-[#ffb800]/60 hover:bg-[#ffb800]/5 focus:outline-none focus:text-[#ffb800] dark:focus:text-[#ffb800] focus:border-[#ffb800]/60 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
