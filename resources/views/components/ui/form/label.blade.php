@props([
    'for' => null,
    'required' => false,
    'value' => null
])

<label
    {{ $attributes->class('block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2') }}
    @if($for) for="{{ $for }}" @endif
>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>
