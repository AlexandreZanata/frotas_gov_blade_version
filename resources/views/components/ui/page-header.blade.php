@props(['title','subtitle'=>null])
<div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div class="space-y-1">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-navy-50">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-gray-500 dark:text-navy-200">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-2" >{{ $actions }}</div>
    @endif
</div>

