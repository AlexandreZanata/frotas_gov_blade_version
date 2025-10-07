@props([
    'action' => null,
    'types' => [],
    'selectedType' => null,
    'selectedAction' => null,
    'searchValue' => null,
])

<div x-data="{ filtersOpen: {{ request()->hasAny(['type', 'action', 'search']) ? 'true' : 'false' }} }" class="mb-4">
    <button
        @click="filtersOpen = !filtersOpen"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-800 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        <span>Filtros de Auditoria</span>
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

        <form method="GET" action="{{ $action ?? route('audit-logs.index') }}" class="space-y-4">
            <!-- Grid de Filtros -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Campo de Busca -->
                <div>
                    <x-input-label for="search" value="Buscar" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <x-text-input
                            id="search"
                            name="search"
                            type="text"
                            class="block w-full pl-10"
                            :value="$searchValue ?? request('search')"
                            placeholder="Usuário, descrição, ID..."
                        />
                    </div>
                </div>

                <!-- Tipo de Registro -->
                <div>
                    <x-input-label for="type" value="Tipo de Registro" />
                    <x-ui.select name="type" id="type" class="mt-1">
                        <option value="">Todos os Tipos</option>
                        @foreach($types as $typeOption)
                            <option value="{{ $typeOption['value'] }}" @selected(($selectedType ?? request('type')) == $typeOption['value'])>
                                {{ $typeOption['label'] }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </div>

                <!-- Ação -->
                <div>
                    <x-input-label for="action" value="Ação" />
                    <x-ui.select name="action" id="action" class="mt-1">
                        <option value="">Todas as Ações</option>
                        <option value="created" @selected(($selectedAction ?? request('action')) == 'created')>Criado</option>
                        <option value="updated" @selected(($selectedAction ?? request('action')) == 'updated')>Atualizado</option>
                        <option value="deleted" @selected(($selectedAction ?? request('action')) == 'deleted')>Excluído</option>
                    </x-ui.select>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex items-center gap-2 pt-2 border-t border-gray-200 dark:border-navy-700">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>Aplicar Filtros</span>
                </button>

                @if(request()->hasAny(['type', 'action', 'search']))
                    <a
                        href="{{ $action ?? route('audit-logs.index') }}"
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

