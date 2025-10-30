@props(['name', 'class' => 'w-5 h-5'])

@php
    $icons = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8v8m5 4H5a2 2 0 01-2-2V9a2 2 0 012-2h3m10 0h3a2 2 0 012 2v11a2 2 0 01-2 2z',
        'users' => 'M17 20h5v-2a4 4 0 00-5-4M9 20H4v-2a4 4 0 015-4m6 0a4 4 0 10-8 0m8 0a4 4 0 01-8 0',
        'clipboard-document-list' => 'M9 12h6m-6 4h6m2 7H7a2 2 0 01-2-2V5a2 2 0 012-2h3m4 0h3a2 2 0 012 2v16a2 2 0 01-2 2z',
        'table-cells' => 'M3 3h18v18H3V3zm9 0v18m9-9H3',
        'arrow-up-tray' => 'M3 16l9-9 9 9M4 20h16',
        'map' => 'M9 3l6 2 6-2v16l-6 2-6-2-6 2V5l6-2z',
        'document-text' => 'M9 12h6m-6 4h6m2 7H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v14a2 2 0 01-2 2z',
        'pencil-square' => 'M16 3l5 5-9 9H7v-5l9-9z',
        'check-circle' => 'M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'arrow-right-on-rectangle' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5',
    ];
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$name] ?? '' }}" />
</svg>
