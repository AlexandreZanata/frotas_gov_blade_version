@props([
    'id' => null,
    'name' => null,
    'type' => 'text',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'placeholder' => '',
    'min' => null,
    'max' => null,
    'step' => null,
    'error' => null
])

@php
    $id = $id ?? $name;
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $hasError = $error || ($errors->has($name) && $name);
@endphp

<input
    type="{{ $type }}"
    @if($id) id="{{ $id }}" @endif
    @if($name) name="{{ $name }}" @endif
    value="{{ $value }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($readonly) readonly @endif
    @if($min !== null) min="{{ $min }}" @endif
    @if($max !== null) max="{{ $max }}" @endif
    @if($step !== null) step="{{ $step }}" @endif
    {{ $attributes->class([
        'w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500',
        'border-red-300 dark:border-red-600 focus:ring-red-500 focus:border-red-500' => $hasError,
        'bg-gray-100 dark:bg-navy-800 text-gray-500 dark:text-navy-400 cursor-not-allowed' => $disabled || $readonly
    ]) }}
/>

@if($hasError)
    @error($name)
    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
@endif
