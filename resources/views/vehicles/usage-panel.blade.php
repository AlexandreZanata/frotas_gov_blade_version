<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Painel de Veículos em Uso"
            subtitle="Controle completo de veículos ativos no sistema"
            icon="chart-bar"
        />
    </x-slot>

    <div class="space-y-6">
        {{-- Cards de Estatísticas (limitado a 4) --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Card Total --}}
            <x-ui.stat-card
                title="Total em Uso"
                :value="$vehicles->total()"
                icon="car"
                variant="default"
            />

            {{-- Cards por Secretaria (limitado a 3) --}}
            @if(isset($stats['bySecretariat']))
                @foreach($stats['bySecretariat']->take(3) as $index => $stat)
                    @php
                        $variants = ['success', 'warning', 'orange'];
                        $variant = $variants[$index % 3];
                    @endphp
                    <x-ui.stat-card
                        :title="$stat->secretariat_name"
                        :value="$stat->total"
                        icon="building-office"
                        :variant="$variant"
                    />
                @endforeach
            @endif
        </div>

        {{-- Gráficos Principais --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Gráfico de Barras --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="chart-bar" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    Veículos por Secretaria
                </h3>

                @if(!empty($chartData['series']) && !empty($chartData['categories']))
                    <x-ui.chart
                        id="vehicles-by-secretariat"
                        type="bar"
                        :data="$chartData"
                        height="300"
                    />
                @else
                    <div class="flex items-center justify-center h-64 text-gray-400">
                        <p>Sem dados para exibir</p>
                    </div>
                @endif
            </x-ui.card>

            {{-- Gráfico de Pizza --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="chart-pie" class="w-5 h-5 text-green-600 dark:text-green-400" />
                    Distribuição Percentual
                </h3>

                @if(!empty($pieChartData['series']) && !empty($pieChartData['labels']))
                    <x-ui.chart
                        id="vehicles-distribution-pie"
                        type="donut"
                        :data="$pieChartData"
                        height="300"
                    />
                @else
                    <div class="flex items-center justify-center h-64 text-gray-400">
                        <p>Sem dados para exibir</p>
                    </div>
                @endif
            </x-ui.card>

            {{-- Gráfico de Status --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="status-online" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    Status Geral
                </h3>

                @if(!empty($statusChartData['series']) && !empty($statusChartData['labels']))
                    <x-ui.chart
                        id="status-chart"
                        type="donut"
                        :data="$statusChartData"
                        height="300"
                    />
                @else
                    <div class="flex items-center justify-center h-64 text-gray-400">
                        <p>Sem dados para exibir</p>
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- Tabela Completa de Veículos em Uso --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <x-icon name="table" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                Todos os Veículos em Uso
            </h3>

            <x-ui.table
                :headers="['Nome','Marca','Ano','Placa','Categoria','Combustível','Status']"
                :searchable="true"
                search-placeholder="Pesquisar por nome, placa, marca ou categoria..."
                :search-value="$search ?? ''"
                :pagination="$vehicles">
                @forelse($vehicles as $vehicle)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                        <td class="px-4 py-2 font-medium">{{ $vehicle->name }}</td>
                        <td class="px-4 py-2">{{ $vehicle->brand }}</td>
                        <td class="px-4 py-2">{{ $vehicle->model_year }}</td>
                        <td class="px-4 py-2 uppercase tracking-wide">{{ $vehicle->plate }}</td>
                        <td class="px-4 py-2">{{ $vehicle->category->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $vehicle->fuelType->name ?? '—' }}</td>
                        <td class="px-4 py-2">
                            <x-ui.status-badge :status="$vehicle->status" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum veículo em uso no momento.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
    </div>
</x-app-layout>
