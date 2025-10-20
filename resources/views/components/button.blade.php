@props([
    'variant' => 'primary',
    'type' => 'button',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center px-4 py-2 border text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';

    $variantClasses = [
        'primary' => 'border-transparent bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500',
        'secondary' => 'border-transparent bg-secondary-600 hover:bg-secondary-700 text-white focus:ring-secondary-500',
        'success' => 'border-transparent bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'danger' => 'border-transparent bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'warning' => 'border-transparent bg-yellow-600 hover:bg-yellow-700 text-white focus:ring-yellow-500',
        'info' => 'border-transparent bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500',
        'light' => 'border-gray-300 bg-white hover:bg-gray-50 text-gray-700 focus:ring-primary-500',
        'dark' => 'border-transparent bg-gray-800 hover:bg-gray-900 text-white focus:ring-gray-500',
        'neutral' => 'border-gray-300 bg-white hover:bg-gray-50 text-gray-700 focus:ring-primary-500',
        'outline' => 'border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500',
    ][$variant] ?? $variantClasses['primary'];

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses . ' ' . $disabledClasses]) }}
>
    {{ $slot }}
</button>
