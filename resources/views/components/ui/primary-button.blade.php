@props(['href' => null, 'type' => 'submit', 'icon' => null, 'compact' => false])
@php( $compact = $compact || filter_var($attributes->get('compact') ?? false, FILTER_VALIDATE_BOOLEAN) )

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white font-semibold tracking-wide shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-navy-900 transition '.($compact ? 'text-xs' : 'text-sm')]) }}>
        @if($icon)
            <x-icon :name="$icon" class="w-4 h-4 shrink-0" />
        @endif
        @if($compact)
            <span class="hidden sm:inline">{{ $slot }}</span>
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white font-semibold tracking-wide shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-navy-900 transition '.($compact ? 'text-xs' : 'text-sm')]) }}>
        @if($icon)
            <x-icon :name="$icon" class="w-4 h-4 shrink-0" />
        @endif
        @if($compact)
            <span class="hidden sm:inline">{{ $slot }}</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif

