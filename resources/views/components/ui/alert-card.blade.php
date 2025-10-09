@props([
    'title',
    'variant' => 'info', // info, warning, danger, success
    'icon' => 'alert-circle',
    'dismissible' => false
])

@php
    $variantClasses = [
        'info' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300',
        'warning' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-300',
        'danger' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300',
        'success' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300',
    ];

    $iconColorClasses = [
        'info' => 'text-blue-600 dark:text-blue-400',
        'warning' => 'text-yellow-600 dark:text-yellow-400',
        'danger' => 'text-red-600 dark:text-red-400',
        'success' => 'text-green-600 dark:text-green-400',
    ];

    $baseClass = $variantClasses[$variant] ?? $variantClasses['info'];
    $iconColor = $iconColorClasses[$variant] ?? $iconColorClasses['info'];
@endphp

<div {{ $attributes->merge(['class' => "rounded-lg border p-4 $baseClass"]) }} x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex items-start gap-3">
        <svg class="w-6 h-6 {{ $iconColor }} shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            @if($icon === 'alert-triangle')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            @elseif($icon === 'check-circle')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @endif
        </svg>
        <div class="flex-1">
            <h3 class="font-semibold mb-2">{{ $title }}</h3>
            <div class="text-sm">
                {{ $slot }}
            </div>
        </div>
        @if($dismissible)
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>
</div>

