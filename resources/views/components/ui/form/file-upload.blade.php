@props([
    'name' => null,
    'label' => 'Selecionar arquivo',
    'preview' => null,
    'accept' => 'image/*',
    'multiple' => false,
    'required' => false,
    'error' => null
])

@php
    $hasError = $error || ($errors->has($name) && $name);
    $inputId = 'file-upload-' . uniqid();
@endphp

<div class="file-upload-wrapper">
    <label for="{{ $inputId }}" class="cursor-pointer block">
        <input type="file"
               id="{{ $inputId }}"
               @if($name) name="{{ $name }}" @endif
               @if($accept) accept="{{ $accept }}" @endif
               @if($multiple) multiple @endif
               @if($required) required @endif
               class="hidden"
            {{ $attributes }}
        >

        <div class="border-2 border-dashed border-gray-300 dark:border-navy-600 rounded-lg p-4 text-center hover:border-primary-500 dark:hover:border-primary-400 transition-colors">
            @if($preview)
                <div class="relative inline-block">
                    <img src="{{ $preview }}" class="h-32 object-cover rounded-lg shadow-sm">
                    <button type="button"
                            {{ $attributes->whereStartsWith('@remove') }}
                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-navy-300 mt-2">Clique para alterar</p>
            @else
                <div class="py-4">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400 dark:text-navy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-navy-300 font-medium">{{ $label }}</p>
                    <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">PNG, JPG, JPEG até 5MB</p>
                </div>
            @endif
        </div>
    </label>

    @if($hasError)
        @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    @endif
</div>
