@props([
    'name',           // نام فایل بدون .png
    'class' => 'w-6 h-6',
    'alt' => ''
])

<img
    src="{{ asset('images/icons/' . $name . '.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => $class]) }}
/>
