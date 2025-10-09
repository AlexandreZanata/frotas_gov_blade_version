@props([
    'label' => null,
    'value' => 0,
    'max' => 100,
    'variant' => 'auto', // auto, success, warning, danger, info
    'showPercentage' => true,
    'size' => 'md' // sm, md, lg
])

@php
    $percentage = $max > 0 ? min(100, ($value / $max) * 100) : 0;

    // Auto variant baseado na porcentagem
    if ($variant === 'auto') {
        if ($percentage >= 100) {
            $variant = 'danger';
        } elseif ($percentage >= 90) {
            $variant = 'orange';
        } elseif ($percentage >= 75) {
            $variant = 'warning';
        } else {
            $variant = 'success';
        }
    }

    $variantClasses = [
        'success' => 'bg-green-500 dark:bg-green-600',
        'warning' => 'bg-yellow-500 dark:bg-yellow-600',
        'orange' => 'bg-orange-500 dark:bg-orange-600',
        'danger' => 'bg-red-600 dark:bg-red-700',
        'info' => 'bg-blue-500 dark:bg-blue-600',
    ];

    $sizeClasses = [
        'sm' => 'h-1.5',
        'md' => 'h-2',
        'lg' => 'h-3',
    ];

    $barColor = $variantClasses[$variant] ?? $variantClasses['success'];
    $barHeight = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label || $showPercentage)
        <div class="flex justify-between text-xs text-gray-600 dark:text-navy-300">
            @if($label)
                <span>{{ $label }}</span>
            @endif
            @if($showPercentage)
                <span>{{ number_format($value, 0, ',', '.') }} / {{ number_format($max, 0, ',', '.') }}</span>
            @endif
        </div>
    @endif
    <div class="w-full bg-gray-200 dark:bg-navy-700 rounded-full {{ $barHeight }} overflow-hidden">
        <div class="{{ $barHeight }} rounded-full {{ $barColor }} transition-all duration-300" style="width: {{ $percentage }}%"></div>
    </div>
</div>

