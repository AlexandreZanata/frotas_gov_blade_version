<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Histórico de Troca de Óleo"
            :subtitle="$vehicle->name . ' - ' . $vehicle->plate"
            hide-title-mobile
            icon="clock"
        />
    </x-slot>

    <x-slot name="pageActions">
        <div class="flex gap-2">
            <button
                @click="$dispatch('open-register-modal', { vehicleId: '{{ $vehicle->id }}' })"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Nova Troca</span>
            </button>
            <a href="{{ route('oil-changes.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white text-sm font-medium shadow transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
                <span>Voltar</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Informações do Veículo -->
        <x-ui.card>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-navy-400 uppercase tracking-wide mb-1">Veículo</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $vehicle->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-navy-400 uppercase tracking-wide mb-1">Placa</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white uppercase">{{ $vehicle->plate }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-navy-400 uppercase tracking-wide mb-1">Categoria</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $vehicle->category->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-navy-400 uppercase tracking-wide mb-1">Total de Trocas</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $vehicle->oilChanges->count() }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Estatísticas do Histórico -->
        @if($vehicle->oilChanges->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-ui.stat-card
                    title="Trocas Realizadas"
                    :value="$vehicle->oilChanges->count()"
                    variant="info"
                    icon="check-circle"
                />

                <x-ui.stat-card
                    title="Custo Total"
                    :value="'R$ ' . number_format($vehicle->oilChanges->sum('cost'), 2, ',', '.')"
                    variant="default"
                    icon="dollar-sign"
                />

                <x-ui.stat-card
                    title="Litros Totais"
                    :value="number_format($vehicle->oilChanges->sum('liters_used'), 1, ',', '.') . ' L'"
                    variant="info"
                    icon="droplet"
                />

                <x-ui.stat-card
                    title="Última Troca"
                    :value="$vehicle->oilChanges->first()->change_date->format('d/m/Y')"
                    variant="success"
                    icon="calendar"
                />
            </div>
        @endif

        <!-- Timeline de Trocas -->
        <x-ui.card title="Histórico Completo" subtitle="Todas as trocas de óleo registradas">
            @if($vehicle->oilChanges->count() > 0)
                <div class="space-y-6">
                    @foreach($vehicle->oilChanges as $index => $oilChange)
                        <div class="relative {{ $loop->last ? '' : 'pb-6' }}">
                            <!-- Linha vertical da timeline -->
                            @if(!$loop->last)
                                <span class="absolute left-4 top-8 -ml-px h-full w-0.5 bg-gray-200 dark:bg-navy-700" aria-hidden="true"></span>
                            @endif

                            <div class="relative flex items-start space-x-3">
                                <!-- Ícone da timeline -->
                                <div class="relative">
                                    <div class="h-8 w-8 rounded-full {{ $loop->first ? 'bg-primary-600 dark:bg-primary-500 ring-8 ring-primary-100 dark:ring-primary-900/30' : 'bg-gray-400 dark:bg-navy-600' }} flex items-center justify-center">
                                        <x-icon name="wrench" class="w-4 h-4 text-white" />
                                    </div>
                                </div>

                                <!-- Conteúdo -->
                                <div class="min-w-0 flex-1">
                                    <div class="bg-gray-50 dark:bg-navy-900 rounded-lg p-5 border border-gray-200 dark:border-navy-700 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                                        <!-- Cabeçalho -->
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-4">
                                            <div>
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white flex items-center gap-2">
                                                    Troca #{{ $vehicle->oilChanges->count() - $index }}
                                                </h3>
                                                <p class="text-sm text-gray-600 dark:text-navy-300 mt-1">
                                                    <x-icon name="calendar" class="w-4 h-4 inline" />
                                                    {{ $oilChange->change_date->format('d/m/Y') }} ({{ $oilChange->change_date->diffForHumans() }})
                                                </p>
                                            </div>
                                            @if($loop->first)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                                                    <x-icon name="star" class="w-3 h-3" />
                                                    Última Troca
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Informações Principais -->
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                            <div class="bg-white dark:bg-navy-800 rounded-lg p-3 border border-gray-200 dark:border-navy-700">
                                                <p class="text-xs text-gray-500 dark:text-navy-400 mb-1">KM na Troca</p>
                                                <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($oilChange->km_at_change, 0, ',', '.') }}</p>
                                                <p class="text-xs text-gray-500 dark:text-navy-400">km</p>
                                            </div>

                                            @if($oilChange->liters_used)
                                                <div class="bg-white dark:bg-navy-800 rounded-lg p-3 border border-gray-200 dark:border-navy-700">
                                                    <p class="text-xs text-gray-500 dark:text-navy-400 mb-1">Litros</p>
                                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($oilChange->liters_used, 1, ',', '.') }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-navy-400">litros</p>
                                                </div>
                                            @endif

                                            @if($oilChange->cost)
                                                <div class="bg-white dark:bg-navy-800 rounded-lg p-3 border border-gray-200 dark:border-navy-700">
                                                    <p class="text-xs text-gray-500 dark:text-navy-400 mb-1">Custo</p>
                                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($oilChange->cost, 2, ',', '.') }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-navy-400">reais</p>
                                                </div>
                                            @endif

                                            <div class="bg-white dark:bg-navy-800 rounded-lg p-3 border border-gray-200 dark:border-navy-700">
                                                <p class="text-xs text-gray-500 dark:text-navy-400 mb-1">Registrado por</p>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" title="{{ $oilChange->user->name }}">{{ $oilChange->user->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-navy-400">{{ $oilChange->created_at->format('d/m/Y') }}</p>
                                            </div>
                                        </div>

                                        <!-- Detalhes Adicionais -->
                                        @if($oilChange->inventoryItem || $oilChange->service_provider || $oilChange->notes)
                                            <div class="space-y-2 pt-4 border-t border-gray-200 dark:border-navy-700">
                                                @if($oilChange->inventoryItem)
                                                    <div class="flex items-start gap-2 text-sm">
                                                        <x-icon name="droplet" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                                                        <div>
                                                            <span class="font-medium text-gray-700 dark:text-navy-200">Tipo de Óleo:</span>
                                                            <span class="text-gray-600 dark:text-navy-300">{{ $oilChange->inventoryItem->name }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($oilChange->service_provider)
                                                    <div class="flex items-start gap-2 text-sm">
                                                        <x-icon name="briefcase" class="w-4 h-4 text-purple-600 dark:text-purple-400 mt-0.5 flex-shrink-0" />
                                                        <div>
                                                            <span class="font-medium text-gray-700 dark:text-navy-200">Prestador:</span>
                                                            <span class="text-gray-600 dark:text-navy-300">{{ $oilChange->service_provider }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($oilChange->notes)
                                                    <div class="flex items-start gap-2 text-sm">
                                                        <x-icon name="file-text" class="w-4 h-4 text-gray-600 dark:text-gray-400 mt-0.5 flex-shrink-0" />
                                                        <div>
                                                            <span class="font-medium text-gray-700 dark:text-navy-200">Observações:</span>
                                                            <span class="text-gray-600 dark:text-navy-300">{{ $oilChange->notes }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Próxima Troca Prevista -->
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700 bg-primary-50 dark:bg-primary-900/10 -mx-5 -mb-5 px-5 py-4 rounded-b-lg">
                                            <p class="text-xs font-semibold text-primary-700 dark:text-primary-400 uppercase tracking-wide mb-3">
                                                <x-icon name="calendar-plus" class="w-3 h-3 inline" />
                                                Próxima Troca Prevista
                                            </p>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-xs text-primary-600 dark:text-primary-400 mb-1">Quilometragem</p>
                                                    <p class="text-base font-bold text-primary-900 dark:text-primary-300">{{ number_format($oilChange->next_change_km, 0, ',', '.') }} km</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-primary-600 dark:text-primary-400 mb-1">Data Prevista</p>
                                                    <p class="text-base font-bold text-primary-900 dark:text-primary-300">{{ $oilChange->next_change_date->format('d/m/Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-navy-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhuma troca registrada</h3>
                    <p class="text-sm text-gray-500 dark:text-navy-400 mb-4">Este veículo ainda não possui histórico de troca de óleo.</p>
                    <button
                        @click="$dispatch('open-register-modal', { vehicleId: '{{ $vehicle->id }}' })"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                        <x-icon name="plus" class="w-4 h-4" />
                        <span>Registrar Primeira Troca</span>
                    </button>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
