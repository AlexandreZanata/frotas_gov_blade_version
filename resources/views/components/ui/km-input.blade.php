@props(['name', 'id' => null, 'value' => '', 'label' => 'Quilometragem'])

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
            {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pr-12']) }}
        >
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <span class="text-gray-500 dark:text-navy-300 text-sm font-medium">KM</span>
        </div>
    </div>
    <x-input-error :messages="$errors->get($name)" />
</div>
@props(['vehicle', 'selected' => false])

<div
    x-data="{
        selected: {{ $selected ? 'true' : 'false' }},
        loading: false
    }"
    @click="$dispatch('vehicle-selected', { id: '{{ $vehicle->id }}' }); selected = true"
    {{ $attributes->merge(['class' => 'cursor-pointer transition-all duration-200']) }}
>
    <div
        class="rounded-lg border-2 p-4 hover:shadow-lg"
        :class="selected ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-navy-600 bg-white dark:bg-navy-800 hover:border-primary-300'"
    >
        <div class="flex items-start justify-between">
            <div class="flex-1 space-y-2">
                <div class="flex items-center gap-3">
                    <x-icon name="car" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-navy-50">
                            {{ $vehicle->prefix->name ?? 'N/A' }} - {{ $vehicle->name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-navy-300">
                            {{ $vehicle->plate }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-navy-300">Categoria:</span>
                        <span class="ml-1 font-medium text-gray-700 dark:text-navy-100">
                            {{ $vehicle->category->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-navy-300">Status:</span>
                        <x-ui.status-badge :status="$vehicle->status->name ?? 'unknown'" class="ml-1" />
                    </div>
                </div>
            </div>

            <div class="ml-4">
                <div
                    class="rounded-full p-2 transition-colors"
                    :class="selected ? 'bg-primary-500 text-white' : 'bg-gray-100 dark:bg-navy-700 text-gray-400'"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

