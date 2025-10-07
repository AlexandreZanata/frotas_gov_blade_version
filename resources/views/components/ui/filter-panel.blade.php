@props([
    'action' => null,
    'method' => 'GET',
])

<div x-data="{ filtersOpen: {{ request()->hasAny(['type', 'action', 'status']) ? 'true' : 'false' }} }" class="mb-4">
    <button
        @click="filtersOpen = !filtersOpen"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-800 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        <span>Filtros</span>
        <svg x-show="!filtersOpen" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        <svg x-show="filtersOpen" x-cloak class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    <div
        x-show="filtersOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="mt-4 p-4 bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-700 rounded-lg shadow-sm">

        <form method="{{ $method }}" action="{{ $action ?? '' }}" class="space-y-4">
            @if($method !== 'GET')
                @csrf
                @method($method)
            @endif

            <!-- Slot para campos de filtro personalizados -->
            <div class="grid gap-4 md:grid-cols-3">
                {{ $slot }}
            </div>

            <!-- Botões de ação -->
            <div class="flex items-center gap-2 pt-2 border-t border-gray-200 dark:border-navy-700">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Aplicar Filtros</span>
                </button>

                @if(request()->hasAny(['type', 'action', 'status', 'search']))
                    <a
                        href="{{ $action ?? url()->current() }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-800 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Limpar Filtros</span>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

