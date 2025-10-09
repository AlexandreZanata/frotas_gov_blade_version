<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Histórico de Troca de Óleo
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $vehicle->name }} - {{ $vehicle->plate }}</p>
            </div>
            <a href="{{ route('oil-changes.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white rounded-md font-medium transition">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if($vehicle->oilChanges->count() > 0)
            <!-- Timeline de Trocas -->
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow border border-gray-200 dark:border-navy-700 p-6">
                <div class="space-y-6">
                    @foreach($vehicle->oilChanges as $index => $oilChange)
                    <div class="relative pl-8 pb-6 {{ $loop->last ? '' : 'border-l-2 border-gray-200 dark:border-navy-700' }}">
                        <!-- Timeline Dot -->
                        <div class="absolute left-0 top-0 -ml-2 w-4 h-4 rounded-full bg-primary-600 dark:bg-primary-500"></div>

                        <div class="bg-gray-50 dark:bg-navy-900 rounded-lg p-4 border border-gray-200 dark:border-navy-700">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        Troca #{{ $vehicle->oilChanges->count() - $index }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-navy-300">
                                        {{ $oilChange->change_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                @if($loop->first)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-primary-100 dark:bg-primary-900/30 text-primary-800 dark:text-primary-300">
                                    Última Troca
                                </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 dark:text-navy-400">KM na Troca</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($oilChange->km_at_change, 0, ',', '.') }} km</p>
                                </div>

                                @if($oilChange->liters_used)
                                <div>
                                    <p class="text-gray-500 dark:text-navy-400">Litros Utilizados</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $oilChange->liters_used }} L</p>
                                </div>
                                @endif

                                @if($oilChange->cost)
                                <div>
                                    <p class="text-gray-500 dark:text-navy-400">Custo</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">R$ {{ number_format($oilChange->cost, 2, ',', '.') }}</p>
                                </div>
                                @endif

                                <div>
                                    <p class="text-gray-500 dark:text-navy-400">Registrado por</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $oilChange->user->name }}</p>
                                </div>
                            </div>

                            @if($oilChange->inventoryItem)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-navy-700">
                                <p class="text-sm text-gray-600 dark:text-navy-300">
                                    <span class="font-medium">Tipo de Óleo:</span> {{ $oilChange->inventoryItem->name }}
                                </p>
                            </div>
                            @endif

                            @if($oilChange->service_provider)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-navy-300">
                                    <span class="font-medium">Prestador:</span> {{ $oilChange->service_provider }}
                                </p>
                            </div>
                            @endif

                            @if($oilChange->notes)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-navy-700">
                                <p class="text-sm text-gray-600 dark:text-navy-300">
                                    <span class="font-medium">Observações:</span> {{ $oilChange->notes }}
                                </p>
                            </div>
                            @endif

                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-navy-700 bg-blue-50 dark:bg-blue-900/20 -m-4 mt-3 p-4 rounded-b-lg">
                                <p class="text-xs text-gray-500 dark:text-navy-400 mb-1">Próxima Troca Prevista:</p>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ number_format($oilChange->next_change_km, 0, ',', '.') }} km</p>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $oilChange->next_change_date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Estatísticas do Veículo -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-navy-800 rounded-lg shadow border border-gray-200 dark:border-navy-700 p-4">
                    <p class="text-sm text-gray-600 dark:text-navy-300 mb-1">Total de Trocas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $vehicle->oilChanges->count() }}</p>
                </div>

                <div class="bg-white dark:bg-navy-800 rounded-lg shadow border border-gray-200 dark:border-navy-700 p-4">
                    <p class="text-sm text-gray-600 dark:text-navy-300 mb-1">Custo Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        R$ {{ number_format($vehicle->oilChanges->sum('cost'), 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white dark:bg-navy-800 rounded-lg shadow border border-gray-200 dark:border-navy-700 p-4">
                    <p class="text-sm text-gray-600 dark:text-navy-300 mb-1">Litros Totais</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($vehicle->oilChanges->sum('liters_used'), 2, ',', '.') }} L
                    </p>
                </div>
            </div>

            @else
            <!-- Sem Histórico -->
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow border border-gray-200 dark:border-navy-700 p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 dark:text-navy-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nenhuma troca registrada</h3>
                <p class="text-gray-600 dark:text-navy-300 mb-4">Este veículo ainda não possui histórico de troca de óleo.</p>
                <a href="{{ route('oil-changes.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition">
                    Registrar Primeira Troca
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>

