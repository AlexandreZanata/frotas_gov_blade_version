@props([
    'title',
    'value',
    'icon',
    'variant' => 'default',
    'clickable' => false,
    'href' => '#'
])

@php
    $variantClasses = match($variant) {
        'success' => 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20',
        'warning' => 'border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20',
        'danger' => 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20',
        'orange' => 'border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20',
        'gray' => 'border-gray-200 dark:border-navy-700 bg-gray-50 dark:bg-navy-800/50',
        default => 'border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800',
    };

    $iconColorClasses = match($variant) {
        'success' => 'text-green-600 dark:text-green-400',
        'warning' => 'text-yellow-600 dark:text-yellow-400',
        'danger' => 'text-red-600 dark:text-red-400',
        'orange' => 'text-orange-600 dark:text-orange-400',
        'gray' => 'text-gray-600 dark:text-navy-400',
        default => 'text-primary-600 dark:text-primary-400',
    };

    $textColorClasses = match($variant) {
        'success' => 'text-green-900 dark:text-green-100',
        'warning' => 'text-yellow-900 dark:text-yellow-100',
        'danger' => 'text-red-900 dark:text-red-100',
        'orange' => 'text-orange-900 dark:text-orange-100',
        'gray' => 'text-gray-900 dark:text-navy-100',
        default => 'text-gray-900 dark:text-white',
    };

    $valueColorClasses = match($variant) {
        'success' => 'text-green-700 dark:text-green-300',
        'warning' => 'text-yellow-700 dark:text-yellow-300',
        'danger' => 'text-red-700 dark:text-red-300',
        'orange' => 'text-orange-700 dark:text-orange-300',
        'gray' => 'text-gray-700 dark:text-navy-300',
        default => 'text-gray-800 dark:text-navy-50',
    };

    $baseClasses = "rounded-lg border shadow-sm p-4 flex items-center gap-3 transition-all duration-200 {$variantClasses}";

    if ($clickable) {
        $baseClasses .= " cursor-pointer hover:shadow-md hover:scale-[1.02] active:scale-[0.98]";
    }
@endphp

@if($clickable)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses]) }}>
        <div class="flex-shrink-0 p-2 rounded-lg bg-white/50 dark:bg-navy-900/50">
            <x-icon :name="$icon" class="w-6 h-6 {{ $iconColorClasses }}" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium {{ $textColorClasses }} truncate">{{ $title }}</p>
            <p class="text-2xl font-bold {{ $valueColorClasses }} mt-0.5">{{ $value }}</p>
        </div>
    </a>
@else
    <div {{ $attributes->merge(['class' => $baseClasses]) }}>
        <div class="flex-shrink-0 p-2 rounded-lg bg-white/50 dark:bg-navy-900/50">
            <x-icon :name="$icon" class="w-6 h-6 {{ $iconColorClasses }}" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium {{ $textColorClasses }} truncate">{{ $title }}</p>
            <p class="text-2xl font-bold {{ $valueColorClasses }} mt-0.5">{{ $value }}</p>
        </div>
    </div>
@endif
