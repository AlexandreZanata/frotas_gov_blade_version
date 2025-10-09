<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Veículos - Gestão de Pneus"
            subtitle="Monitoramento e manutenção dos pneus por veículo"
            hide-title-mobile
            icon="car"
        />
    </x-slot>

    <x-slot name="pageActions">
        <a href="{{ route('tires.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white text-sm font-medium shadow transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Voltar</span>
        </a>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div x-data="{
            searchQuery: '',

            filterVehicles(vehicle) {
                if (!this.searchQuery) return true;
                const query = this.searchQuery.toLowerCase();
                return vehicle.name.toLowerCase().includes(query) ||
                       vehicle.plate.toLowerCase().includes(query);
            }
        }">
            <!-- Filtro de Busca -->
            <x-ui.card title="Buscar Veículos" subtitle="Encontre rapidamente um veículo" class="mb-6">
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Digite o nome ou placa do veículo..."
                       class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </x-ui.card>

            <!-- Grid de Veículos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($vehicles as $vehicle)
                    @php
                        $tiresWithCondition = $vehicle->tires->groupBy('condition');
                        $criticalCount = $tiresWithCondition->get('Crítico', collect())->count();
                        $attentionCount = $tiresWithCondition->get('Atenção', collect())->count();
                        $totalTires = $vehicle->tires->count();
                        $goodCount = $tiresWithCondition->get('Novo', collect())->count() +
                                     $tiresWithCondition->get('Bom', collect())->count();
                    @endphp

                    <div x-show="filterVehicles({
                            name: '{{ $vehicle->name }}',
                            plate: '{{ $vehicle->plate }}'
                         })"
                         class="bg-white dark:bg-navy-800 rounded-lg shadow-sm border border-gray-200 dark:border-navy-700 p-5 hover:shadow-lg transition cursor-pointer"
                         onclick="window.location.href='{{ route('tires.vehicles.show', $vehicle->id) }}'">

                        <!-- Alerta -->
                        @if($criticalCount > 0)
                            <div class="mb-3 px-3 py-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-md">
                                <div class="flex items-center gap-2 text-red-700 dark:text-red-300 text-sm font-medium">
                                    <x-icon name="alert-triangle" class="w-4 h-4" />
                                    <span>{{ $criticalCount }} pneu(s) crítico(s)</span>
                                </div>
                            </div>
                        @elseif($attentionCount > 0)
                            <div class="mb-3 px-3 py-2 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-md">
                                <div class="flex items-center gap-2 text-yellow-700 dark:text-yellow-300 text-sm font-medium">
                                    <x-icon name="alert-circle" class="w-4 h-4" />
                                    <span>{{ $attentionCount }} pneu(s) em atenção</span>
                                </div>
                            </div>
                        @endif

                        <!-- Cabeçalho -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $vehicle->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-navy-400">{{ $vehicle->plate }}</p>
                                <p class="text-xs text-gray-400 dark:text-navy-500 mt-1">
                                    {{ $vehicle->category->name ?? 'Sem categoria' }}
                                </p>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                    {{ $totalTires }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-navy-400">pneus</div>
                            </div>
                        </div>

                        <!-- Barra de Condição -->
                        @if($totalTires > 0)
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 dark:text-navy-400 mb-2">
                                    <span>Condição Geral</span>
                                    <span>{{ number_format(($goodCount / $totalTires) * 100, 0) }}% bom</span>
                                </div>
                                <div class="flex gap-1 h-3 rounded-full overflow-hidden bg-gray-200 dark:bg-navy-700">
                                    @if($goodCount > 0)
                                        <div class="bg-green-500" style="width: {{ ($goodCount / $totalTires) * 100 }}%"></div>
                                    @endif
                                    @if($attentionCount > 0)
                                        <div class="bg-yellow-500" style="width: {{ ($attentionCount / $totalTires) * 100 }}%"></div>
                                    @endif
                                    @if($criticalCount > 0)
                                        <div class="bg-red-500" style="width: {{ ($criticalCount / $totalTires) * 100 }}%"></div>
                                    @endif
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 dark:text-navy-400 mt-1">
                                    <span>🟢 {{ $goodCount }}</span>
                                    <span>🟡 {{ $attentionCount }}</span>
                                    <span>🔴 {{ $criticalCount }}</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 text-sm text-gray-500 dark:text-navy-400">
                                Nenhum pneu instalado
                            </div>
                        @endif

                        <!-- Ação -->
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700">
                            <div class="text-sm text-primary-600 dark:text-primary-400 font-medium text-center">
                                Ver Diagrama →
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mensagem se não houver veículos -->
            @if($vehicles->count() === 0)
                <x-ui.alert-card title="Nenhum Veículo Encontrado" variant="info" icon="info">
                    <p>Não há veículos cadastrados no sistema. Cadastre veículos primeiro para gerenciar seus pneus.</p>
                </x-ui.alert-card>
            @endif
        </div>
    </div>
</x-app-layout>
