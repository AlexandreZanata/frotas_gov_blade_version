<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Estoque de Pneus"
            subtitle="Gerenciamento de pneus disponíveis no estoque"
            hide-title-mobile
            icon="cube"
        />
    </x-slot>

    <x-slot name="pageActions">
        <a href="{{ route('tires.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Cadastrar Pneu</span>
        </a>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div x-data="{
            searchQuery: '',
            conditionFilter: '',

            filterTires(tire) {
                const query = this.searchQuery.toLowerCase();
                const matchesSearch = !query ||
                    tire.brand.toLowerCase().includes(query) ||
                    tire.model.toLowerCase().includes(query) ||
                    tire.serial.toLowerCase().includes(query);

                const matchesCondition = !this.conditionFilter || tire.condition === this.conditionFilter;

                return matchesSearch && matchesCondition;
            }
        }">
            <!-- Filtros -->
            <x-ui.card title="Filtros" subtitle="Buscar e filtrar pneus no estoque" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="search" value="Buscar" />
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Marca, modelo ou número de série..."
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div>
                        <x-input-label for="condition" value="Condição" />
                        <select
                            x-model="conditionFilter"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Todas as condições</option>
                            <option value="Novo">Novo</option>
                            <option value="Bom">Bom</option>
                            <option value="Atenção">Atenção</option>
                            <option value="Crítico">Crítico</option>
                        </select>
                    </div>
                </div>
            </x-ui.card>

            <!-- Lista de Pneus -->
            <x-ui.card title="Pneus em Estoque" subtitle="{{ $tires->total() }} pneus disponíveis">
                @if($tires->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($tires as $tire)
                            <div
                                x-show="filterTires({
                                    brand: '{{ $tire->brand }}',
                                    model: '{{ $tire->model }}',
                                    serial: '{{ $tire->serial_number }}',
                                    condition: '{{ $tire->condition }}'
                                })"
                                class="bg-gray-50 dark:bg-navy-900 rounded-lg p-4 border border-gray-200 dark:border-navy-700 hover:border-primary-300 dark:hover:border-primary-600 transition">

                                <!-- Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $tire->brand }} {{ $tire->model }}
                                        </h3>
                                        <p class="text-xs text-gray-500 dark:text-navy-400 mt-0.5">
                                            SN: {{ $tire->serial_number }}
                                        </p>
                                    </div>

                                    <!-- Condição Badge -->
                                    @php
                                        $conditionColors = [
                                            'Novo' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                            'Bom' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            'Atenção' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            'Crítico' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $conditionColors[$tire->condition] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $tire->condition }}
                                    </span>
                                </div>

                                <!-- Progresso -->
                                @php
                                    $usagePercent = ($tire->current_km / $tire->lifespan_km) * 100;
                                    $progressColor = $usagePercent < 30 ? 'bg-green-500' :
                                                   ($usagePercent < 70 ? 'bg-blue-500' :
                                                   ($usagePercent < 90 ? 'bg-yellow-500' : 'bg-red-500'));
                                @endphp
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-600 dark:text-navy-400 mb-1">
                                        <span>Vida Útil</span>
                                        <span>{{ number_format($usagePercent, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-navy-700 rounded-full h-2">
                                        <div class="{{ $progressColor }} h-2 rounded-full transition-all" style="width: {{ min($usagePercent, 100) }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">
                                        {{ number_format($tire->current_km) }} / {{ number_format($tire->lifespan_km) }} km
                                    </p>
                                </div>

                                <!-- Info adicional -->
                                <div class="space-y-1 text-xs text-gray-600 dark:text-navy-400 mb-3">
                                    @if($tire->dot_number)
                                        <p><span class="font-medium">DOT:</span> {{ $tire->dot_number }}</p>
                                    @endif
                                    <p><span class="font-medium">Compra:</span> {{ \Carbon\Carbon::parse($tire->purchase_date)->format('d/m/Y') }}</p>
                                    @if($tire->purchase_price)
                                        <p><span class="font-medium">Preço:</span> R$ {{ number_format($tire->purchase_price, 2, ',', '.') }}</p>
                                    @endif
                                </div>

                                <!-- Ações -->
                                <div class="flex gap-2">
                                    <a href="{{ route('tires.history', $tire->id) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-navy-100 bg-white dark:bg-navy-800 border border-gray-300 dark:border-navy-600 rounded-md hover:bg-gray-50 dark:hover:bg-navy-700 transition">
                                        <x-icon name="clock" class="w-3.5 h-3.5" />
                                        Histórico
                                    </a>
                                    <button
                                        onclick="alert('Funcionalidade de edição em desenvolvimento')"
                                        class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-navy-100 bg-white dark:bg-navy-800 border border-gray-300 dark:border-navy-600 rounded-md hover:bg-gray-50 dark:hover:bg-navy-700 transition">
                                        <x-icon name="pencil" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $tires->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-navy-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Nenhum pneu em estoque</h3>
                        <p class="text-gray-500 dark:text-navy-400 mb-4">Comece cadastrando novos pneus no sistema.</p>
                        <a href="{{ route('tires.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                            <x-icon name="plus" class="w-4 h-4" />
                            <span>Cadastrar Primeiro Pneu</span>
                        </a>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
