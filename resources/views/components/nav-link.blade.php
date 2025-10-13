@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-0 rounded-md text-sm font-bold leading-5 bg-blue-100 text-blue-700 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-0 rounded-md text-sm font-medium leading-5 text-gray-800 hover:text-blue-700 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>