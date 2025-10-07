@props([
    'href' => null,
    'icon',
    'title' => '',
    'variant' => 'primary', // primary, neutral, info, success, warning, danger
    'size' => 'sm', // sm, md
    'as' => 'a', // a ou button
    'type' => 'button'
])
@php
    $sizes = [
        'sm' => 'h-7 w-7',
        'md' => 'h-9 w-9',
    ];
    $variants = [
        'primary' => 'text-primary-600 hover:bg-primary-50 dark:text-navy-100 dark:hover:bg-navy-700/60',
        'neutral' => 'text-gray-600 hover:bg-gray-100 dark:text-navy-100 dark:hover:bg-navy-700/60',
        'info' => 'text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-900/30',
        'success' => 'text-emerald-600 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-900/30',
        'warning' => 'text-amber-600 hover:bg-amber-50 dark:text-amber-300 dark:hover:bg-amber-900/30',
        'danger' => 'text-red-600 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/30',
    ];
    $classes = ($sizes[$size] ?? $sizes['sm']).' inline-flex items-center justify-center rounded-md transition '.($variants[$variant] ?? $variants['primary']);
@endphp
@if($as === 'button')
    <button type="{{ $type }}" title="{{ $title }}" {{ $attributes->merge(['class'=>$classes]) }}>
        <x-icon :name="$icon" class="w-4 h-4" />
        <span class="sr-only">{{ $title }}</span>
    </button>
@else
    <a href="{{ $href }}" title="{{ $title }}" {{ $attributes->merge(['class'=>$classes]) }}>
        <x-icon :name="$icon" class="w-4 h-4" />
        <span class="sr-only">{{ $title }}</span>
    </a>
@endif

