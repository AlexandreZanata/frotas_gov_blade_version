@props(['title','subtitle'=>null,'hideTitleMobile'=>false])

<div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div class="space-y-1 min-w-0">
        <h1 class="font-semibold tracking-tight text-gray-800 dark:text-navy-50 text-xl md:text-2xl flex items-center gap-2"
            @class(['hidden md:flex'=>$hideTitleMobile,'flex'=>!$hideTitleMobile])>
            <span>{{ $title }}</span>
        </h1>
        @if($subtitle)
            <p class="text-sm text-gray-500 dark:text-navy-200 leading-snug {{ $hideTitleMobile ? 'block' : '' }}">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex flex-wrap items-center gap-2 shrink-0">{{ $actions }}</div>
    @endif
</div>
