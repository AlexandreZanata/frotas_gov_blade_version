@props([
    'icon' => 'document-text',
    'title' => 'Nenhum item encontrado',
    'description' => null,
    'size' => 'default' // 'sm', 'default', 'lg'
])

@php
    $sizeClasses = [
        'sm' => 'py-4',
        'default' => 'py-8',
        'lg' => 'py-12'
    ];

    $iconSize = [
        'sm' => 'w-8 h-8',
        'default' => 'w-12 h-12',
        'lg' => 'w-16 h-16'
    ];
@endphp

<div {{ $attributes->class([
    'text-center',
    $sizeClasses[$size] ?? $sizeClasses['default']
]) }}>
    @if($icon)
        <div class="mx-auto mb-3 text-gray-400 dark:text-navy-500">
            <svg class="{{ $iconSize[$size] ?? $iconSize['default'] }} mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($icon === 'document-text')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                @elseif($icon === 'building-store')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                @elseif($icon === 'currency-dollar')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
                @endif
            </svg>
        </div>
    @endif

    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</h3>

    @if($description)
        <p class="mt-1 text-sm text-gray-500 dark:text-navy-300">{{ $description }}</p>
    @endif

    @if(isset($actions))
        <div class="mt-4">
            {{ $actions }}
        </div>
    @endif
</div>
