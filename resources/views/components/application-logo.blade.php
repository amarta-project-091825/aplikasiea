@props(['maxHeight' => 'h-50'])

<img 
    src="{{ asset('images/Frame.svg') }}" 
    alt="Logo" 
    {{ $attributes->merge(['class' => "max-h-full w-auto block dark:invert {$maxHeight}"]) }}
/>

<img 
    src="{{ asset('images/Frame.svg') }}" 
    alt="Logo" 
    {{ $attributes->merge(['class' => "max-h-full w-auto hidden dark:invert {$maxHeight}"]) }}
/>
