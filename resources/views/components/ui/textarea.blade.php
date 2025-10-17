@props([
    'disabled' => false,
    'withErrors' => false,
])

@php
    $baseClasses = 'block w-full text-sm transition duration-150 ease-in-out border-gray-300 dark:border-navy-500 dark:bg-navy-700 dark:text-navy-100 focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50 rounded-md shadow-sm';

    // Adiciona classes de erro se houver erros de validação para este campo
    $errorClasses = $withErrors && $errors->has($attributes->get('name'))
        ? 'border-red-500 dark:border-red-500 focus:border-red-500 focus:ring-red-200'
        : '';

    $classes = $baseClasses . ' ' . $errorClasses;
@endphp

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>{{ $slot }}</textarea>
