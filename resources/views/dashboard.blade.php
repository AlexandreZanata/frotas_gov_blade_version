<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    {{-- Alertas de Gaps de Quilometragem --}}
    @if(Auth::user()->hasAnyRole(['general_manager', 'sector_manager']) && $recentGaps->isNotEmpty())
        <div class="mb-6">
            <x-ui.card class="border-l-4 border-l-red-500 bg-red-50 dark:bg-red-900/20">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 flex items-center gap-2">
                        <x-icon name="alert" class="w-4 h-4"/>
                        Alertas de Quilometragem Inconsistente
                    </h3>
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                    {{ $recentGaps->count() }} alerta(s)
                </span>
                </div>

                <div class="space-y-3">
                    @foreach($recentGaps as $gap)
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-800">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                        <x-icon name="calendar" class="w-5 h-5 text-red-600 dark:text-red-400"/>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $gap->vehicle->name ?? 'Veículo não encontrado' }}
                                        <span class="text-sm text-gray-500 dark:text-gray-400">({{ $gap->vehicle->plate ?? 'N/A' }})</span>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        <strong>Condutor:</strong> {{ $gap->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $gap->created_at->format('d/m/Y \à\s H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-red-600 dark:text-red-400">
                                    +{{ $gap->gap_km }} km
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    <div>Esperado: {{ $gap->expected_start_km }} km</div>
                                    <div>Registrado: {{ $gap->recorded_start_km }} km</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-red-200 dark:border-red-800">
                    <p class="text-sm text-red-700 dark:text-red-300">
                        <strong>Atenção:</strong> Estes gaps indicam inconsistências na quilometragem dos veículos.
                        Verifique se há problemas no registro de quilometragem ou uso não autorizado.
                    </p>
                </div>
            </x-ui.card>
        </div>
    @endif

    <div class="space-y-6">
        {{-- Painel de Veículos em Uso (apenas para gestores) --}}
        @if(Auth::user()->hasAnyRole(['general_manager', 'sector_manager']))
            <div class="space-y-6">
                @if(count($vehiclesInUse) > 0)
                    {{-- Cards de Estatísticas (limitado a 4 cards) --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Card Total --}}
                        <x-ui.stat-card
                            title="Total em Uso"
                            :value="$stats['total']"
                            icon="truck"
                            variant="default"
                        />

                        {{-- Cards por Secretaria (limitado a 3) --}}
                        @foreach($stats['bySecretariat']->take(3) as $index => $stat)
                            @php
                                $variants = ['success', 'warning', 'orange'];
                                $variant = $variants[$index % 3];
                            @endphp
                            <x-ui.stat-card
                                :title="$stat->secretariat_name"
                                :value="$stat->total"
                                icon="building"
                                :variant="$variant"
                            />
                        @endforeach
                    </div>

                    {{-- Grid: Tabela + Gráficos --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Tabela de Veículos em Uso --}}
                        <x-ui.card>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                    <x-icon name="calendar" class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                                    Veículos Ativos Agora
                                </h3>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                    <tr class="text-left border-b dark:border-gray-700">
                                        <th class="pb-2 font-semibold text-gray-700 dark:text-gray-300">Veículo</th>
                                        <th class="pb-2 font-semibold text-gray-700 dark:text-gray-300">Usuário</th>
                                        <th class="pb-2 font-semibold text-gray-700 dark:text-gray-300">Destino</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y dark:divide-gray-700">
                                    @foreach($vehiclesInUse as $vehicle)
                                        @php
                                            $run = $vehicle->runs->first();
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="py-3">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $vehicle->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $vehicle->plate }}
                                                </div>
                                            </td>
                                            <td class="py-3 text-gray-700 dark:text-gray-300">
                                                {{ $run->user->name ?? '-' }}
                                            </td>
                                            <td class="py-3 text-gray-700 dark:text-gray-300">
                                                <div class="truncate max-w-[150px]" title="{{ $run->destination ?? '-' }}">
                                                    {{ $run->destination ?? '-' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Botão Ver Mais --}}
                            <div class="mt-4 pt-4 border-t dark:border-gray-700">
                                <a href="{{ route('vehicles.usage-panel') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                                    <x-icon name="eye" class="w-5 h-5"/>

                                    <span>Ver Detalhes Completos</span>
                                </a>
                            </div>
                        </x-ui.card>

                        {{-- Gráfico de Veículos por Secretaria --}}
                        <x-ui.card>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                                    <x-icon name="pie-chart" class="w-5 h-5 text-green-600 dark:text-green-400"/>
                                Distribuição por Secretaria
                            </h3>

                            @if(!empty($chartData['series']) && !empty($chartData['categories']))
                                <x-ui.chart
                                    id="dashboard-vehicles-chart"
                                    type="donut"
                                    :data="$chartData"
                                    height="280"
                                />
                            @else
                                <div class="flex items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <p>Sem dados para exibir</p>
                                    </div>
                                </div>
                            @endif
                        </x-ui.card>
                    </div>

                    {{-- Gráficos adicionais de estatísticas --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Gráfico de Gastos --}}
                        <x-ui.card>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                                    <x-icon name="coin" class="w-5 h-5 text-red-600 dark:text-red-400"/>
                                Gastos (Últimos 7 dias)
                            </h3>
                            @if(!empty($expensesData['series']) && !empty($expensesData['categories']))
                                <x-ui.chart
                                    id="expenses-chart"
                                    type="area"
                                    :data="$expensesData"
                                    height="280"
                                />
                            @else
                                <div class="flex items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                                    <div class="text-center">
                                        <x-icon name="coin" class="w-5 h-5 text-red-600 dark:text-red-400"/>

                                        <p>Sem dados de gastos</p>
                                    </div>
                                </div>
                            @endif
                        </x-ui.card>

                        {{-- Gráfico de Abastecimentos --}}
                        <x-ui.card>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                                    <x-icon name="speedometer2" class="w-5 h-5 text-yellow-600 dark:text-yellow-400"/>

                                Abastecimentos (Últimos 7 dias)
                            </h3>
                            @if(!empty($fuelingsData['series']) && !empty($fuelingsData['categories']))
                                <x-ui.chart
                                    id="fuelings-chart"
                                    type="bar"
                                    :data="$fuelingsData"
                                    height="280"
                                />
                            @else
                                <div class="flex items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p>Sem dados de abastecimentos</p>
                                    </div>
                                </div>
                            @endif
                        </x-ui.card>
                    </div>
                @else
                    {{-- Mensagem quando não há veículos em uso --}}
                    <x-ui.card>
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Nenhum veículo em uso no momento
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">
                                Quando um veículo for utilizado, as estatísticas aparecerão aqui.
                            </p>
                            <a href="{{ route('logbook.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-md shadow transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Iniciar Diário de Bordo</span>
                            </a>
                        </div>
                    </x-ui.card>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
