@props([
    'id' => null,
    'name' => null,
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'rows' => 3,
    'error' => null
])

@php
    $id = $id ?? $name;
    $hasError = $error || ($errors->has($name) && $name);
@endphp

<textarea
    @if($id) id="{{ $id }}" @endif
@if($name) name="{{ $name }}" @endif
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif
    rows="{{ $rows }}"
    {{ $attributes->class([
        'w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500',
        'border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500' => $hasError,
        'bg-gray-100 dark:bg-navy-800 text-gray-500 dark:text-navy-400 cursor-not-allowed' => $disabled || $readonly
    ]) }}
>{{ $value ?? $slot }}</textarea>

@if($hasError)
    @error($name)
    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
@endif
