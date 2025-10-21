<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Painel de Gastos com Combustível"
            subtitle="Visão geral dos gastos por veículo e posto"
            hide-title-mobile
            icon="currency-dollar"
        />
    </x-slot>

    <div class="space-y-8">
        {{-- Cards de Estatísticas Minimalistas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card Total Geral --}}
            <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Total Gasto</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</p>
                    <div class="w-16 h-1 bg-gradient-to-r from-blue-500 to-blue-600 mx-auto rounded-full"></div>
                </div>
            </div>

            {{-- Card Total por Veículo --}}
            <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Veículos Monitorados</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $vehicleExpenses->total() }}</p>
                    <div class="w-16 h-1 bg-gradient-to-r from-green-500 to-green-600 mx-auto rounded-full"></div>
                </div>
            </div>

            {{-- Card Postos --}}
            <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Postos Cadastrados</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $gasStationExpenses ? $gasStationExpenses->total() : '-' }}
                    </p>
                    <div class="w-16 h-1 bg-gradient-to-r from-amber-500 to-amber-600 mx-auto rounded-full"></div>
                </div>
            </div>

            {{-- Card Previsão Mensal --}}
            <div x-data="expenseForecast()" x-init="loadForecast()"
                 class="bg-white dark:bg-navy-800 rounded-2xl shadow-sm border border-gray-100 dark:border-navy-700 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Previsão Próximo Mês</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1" x-text="forecastValue"></p>
                    <div class="w-16 h-1 bg-gradient-to-r from-purple-500 to-purple-600 mx-auto rounded-full"></div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2" x-show="forecastData">
                        Tendência: <span x-text="forecastData.analysis.trend_direction"
                                         :class="forecastData.analysis.trend_direction === 'aumentando' ? 'text-red-500' :
                                       forecastData.analysis.trend_direction === 'diminuindo' ? 'text-green-500' : 'text-gray-500'"
                                         class="font-semibold capitalize"></span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Tabela de Gastos por Veículo --}}
        <x-ui.card class="rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Gastos por Veículo</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total: {{ $vehicleExpenses->total() }} veículos
                </div>
            </div>

            <x-ui.table
                :headers="['Veículo', 'Placa', 'Secretaria', 'Total Gasto (R$)', 'Ações']"
                :searchable="true"
                search-placeholder="Pesquisar veículo..."
                :pagination="$vehicleExpenses">
                @forelse($vehicleExpenses as $expense)
                    <tr class="border-b border-gray-100 dark:border-navy-700 hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $expense->vehicle->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 uppercase">
                                {{ $expense->vehicle->plate ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $expense->vehicle->secretariat->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900 dark:text-white">R$ {{ number_format($expense->total_fuel_cost, 2, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <x-ui.action-icon
                                :href="route('fueling_expenses.vehicle_details', $expense->vehicle_id)"
                                icon="eye"
                                title="Ver Detalhes"
                                variant="primary"
                                class="hover:scale-110 transition-transform"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <div class="text-gray-400 dark:text-gray-500">
                                <x-icon name="document-search" class="w-12 h-12 mx-auto mb-3 opacity-50" />
                                <p class="text-lg font-medium">Nenhum gasto encontrado</p>
                                <p class="text-sm">Não há registros de gastos com combustível no momento.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        {{-- Tabela de Gastos por Posto (apenas para general_manager) --}}
        @if($gasStationExpenses)
            <x-ui.card class="rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Gastos por Posto de Combustível</h3>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Total: {{ $gasStationExpenses->total() }} postos
                    </div>
                </div>

                <x-ui.table
                    :headers="['Posto', 'Total Gasto (R$)', 'Ações']"
                    :searchable="true"
                    search-placeholder="Pesquisar posto..."
                    :pagination="$gasStationExpenses">
                    @forelse($gasStationExpenses as $expense)
                        <tr class="border-b border-gray-100 dark:border-navy-700 hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $expense->gasStation->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900 dark:text-white">R$ {{ number_format($expense->total_fuel_cost, 2, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-ui.action-icon
                                    :href="route('fueling_expenses.station_details', $expense->gas_station_id)"
                                    icon="eye"
                                    title="Ver Detalhes"
                                    variant="primary"
                                    class="hover:scale-110 transition-transform"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center">
                                <div class="text-gray-400 dark:text-gray-500">
                                    <x-icon name="building-store" class="w-12 h-12 mx-auto mb-3 opacity-50" />
                                    <p class="text-lg font-medium">Nenhum gasto encontrado</p>
                                    <p class="text-sm">Não há registros de gastos por posto no momento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-ui.table>
            </x-ui.card>
        @endif

        {{-- Gráfico de Previsão --}}
        <div x-data="expenseForecast()" x-init="loadForecast()">
            <x-ui.card class="rounded-2xl">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Previsão de Gastos com Combustível</h3>

                <div x-show="loading" class="flex items-center justify-center h-64">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mb-3"></div>
                        <p class="text-gray-500 dark:text-gray-400">Carregando previsão...</p>
                    </div>
                </div>

                <div x-show="!loading && forecastData" class="space-y-6">
                    <div id="expense-forecast-chart" style="height: 300px;"></div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-navy-700 rounded-xl p-4 text-center">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Direção da Tendência</div>
                            <div x-text="forecastData.analysis.trend_direction"
                                 :class="trendDirectionClass"
                                 class="text-lg font-bold capitalize"></div>
                        </div>
                        <div class="bg-gray-50 dark:bg-navy-700 rounded-xl p-4 text-center">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Variação Mensal</div>
                            <div x-text="monthlyChangeText"
                                 class="text-lg font-bold text-gray-900 dark:text-white"></div>
                        </div>
                        <div class="bg-gray-50 dark:bg-navy-700 rounded-xl p-4 text-center">
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Confiança da Previsão</div>
                            <div x-text="confidenceLevel"
                                 :class="confidenceClass"
                                 class="text-lg font-bold"></div>
                        </div>
                    </div>
                </div>

                <div x-show="!loading && error" class="text-center py-8">
                    <x-icon name="alert" class="w-16 h-16 text-red-400 mx-auto mb-4" />
                    <p class="text-red-600 dark:text-red-400 text-lg font-medium" x-text="error"></p>
                </div>
            </x-ui.card>
        </div>
    </div>

    <script>
        function expenseForecast() {
            return {
                loading: true,
                forecastData: null,
                error: null,
                forecastValue: 'R$ 0,00',
                chart: null,

                get trendDirectionClass() {
                    if (!this.forecastData) return 'text-gray-600 dark:text-gray-400';

                    return {
                        'aumentando': 'text-red-600 dark:text-red-400',
                        'diminuindo': 'text-green-600 dark:text-green-400',
                        'estável': 'text-gray-600 dark:text-gray-400'
                    }[this.forecastData.analysis.trend_direction] || 'text-gray-600 dark:text-gray-400';
                },

                get monthlyChangeText() {
                    if (!this.forecastData) return 'R$ 0,00';
                    return 'R$ ' + Number(this.forecastData.analysis.monthly_change_value).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                },

                get confidenceLevel() {
                    if (!this.forecastData) return 'Baixa';
                    return this.forecastData.historical_trend.length >= 6 ? 'Alta' : 'Média';
                },

                get confidenceClass() {
                    if (!this.forecastData) return 'text-red-600 dark:text-red-400';
                    return this.forecastData.historical_trend.length >= 6 ?
                        'text-green-600 dark:text-green-400' :
                        'text-yellow-600 dark:text-yellow-400';
                },

                renderChart() {
                    if (!this.forecastData || !window.ApexCharts) return;

                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const chartData = {
                        series: [
                            {
                                name: 'Gastos Reais',
                                data: this.forecastData.historical_trend.map(item => item.actual_cost)
                            },
                            {
                                name: 'Tendência',
                                data: this.forecastData.historical_trend.map(item => item.trend_cost)
                            },
                            {
                                name: 'Previsão',
                                data: [
                                    ...Array(this.forecastData.historical_trend.length - 1).fill(null),
                                    this.forecastData.historical_trend[this.forecastData.historical_trend.length - 1].trend_cost,
                                    ...this.forecastData.forecast_next_6_months.map(item => item.predicted_cost)
                                ]
                            }
                        ],
                        categories: [
                            ...this.forecastData.historical_trend.map(item => item.label),
                            ...this.forecastData.forecast_next_6_months.map(item => item.label)
                        ]
                    };

                    const options = {
                        chart: {
                            type: 'line',
                            height: 300,
                            toolbar: { show: true },
                            fontFamily: 'Inter, sans-serif',
                            background: 'transparent'
                        },
                        series: chartData.series,
                        xaxis: {
                            categories: chartData.categories,
                            labels: {
                                rotate: -45,
                                style: {
                                    colors: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#9CA3AF' : '#374151'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                },
                                style: {
                                    colors: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#9CA3AF' : '#374151'
                                }
                            }
                        },
                        stroke: {
                            width: [3, 3, 3],
                            dashArray: [0, 5, 0]
                        },
                        colors: ['#3B82F6', '#8B5CF6', '#10B981'],
                        markers: { size: 4 },
                        legend: {
                            position: 'top',
                            labels: {
                                colors: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#9CA3AF' : '#374151'
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                                }
                            }
                        },
                        grid: {
                            borderColor: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#374151' : '#E5E7EB'
                        }
                    };

                    this.chart = new ApexCharts(document.querySelector("#expense-forecast-chart"), options);
                    this.chart.render();
                },

                async loadForecast() {
                    try {
                        this.loading = true;
                        const response = await fetch('{{ route("fueling_expenses.forecast") }}');
                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.error || 'Erro ao carregar previsão');
                        }

                        this.forecastData = data;

                        if (data.forecast_next_6_months && data.forecast_next_6_months[0]) {
                            this.forecastValue = 'R$ ' +
                                Number(data.forecast_next_6_months[0].predicted_cost)
                                    .toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                        }

                        setTimeout(() => {
                            this.renderChart();
                        }, 100);

                    } catch (err) {
                        this.error = err.message;
                        console.error('Erro ao carregar previsão:', err);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ApexCharts === 'undefined') {
                console.warn('ApexCharts não está carregado. O gráfico de previsão não será renderizado.');
            }
        });
    </script>
</x-app-layout>
