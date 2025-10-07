@props(['title' => null, 'subtitle' => null, 'padding' => 'p-5'])
<div {{ $attributes->merge(['class' => "rounded-lg border border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800 shadow-sm $padding space-y-4"]) }}>
    @if($title || $subtitle)
        <div class="space-y-1">
            @if($title)
                <h2 class="text-lg font-semibold text-gray-800 dark:text-navy-50">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-500 dark:text-navy-200">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    <div class="space-y-4">
        {{ $slot }}
    </div>
</div>

