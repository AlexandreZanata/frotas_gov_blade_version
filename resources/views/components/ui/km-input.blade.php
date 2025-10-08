@props(['name', 'id' => null, 'value' => '', 'label' => 'Quilometragem', 'required' => false])

<div class="space-y-2">
    <x-input-label :for="$id ?? $name" :value="$label" />
    <div class="relative">
        <input
            type="number"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            value="{{ old($name, $value) }}"
            min="0"
            step="1"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pr-12']) }}
        >
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <span class="text-gray-500 dark:text-navy-300 text-sm font-medium">KM</span>
        </div>
    </div>
    <x-input-error :messages="$errors->get($name)" />
</div>
