<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Gestão de Pneus"
            subtitle="Dashboard de monitoramento e controle da vida útil dos pneus"
            hide-title-mobile
            icon="circle"
        />
    </x-slot>

    <x-slot name="pageActions">
        <a href="{{ route('tires.stock') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white text-sm font-medium shadow transition">
            <x-icon name="cube" class="w-4 h-4" />
            <span>Estoque</span>
        </a>
        <a href="{{ route('tires.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Novo Pneu</span>
        </a>
    </x-slot>

    <!-- Estatísticas Vitais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Pneus Críticos -->
        <x-ui.stat-card
            title="Pneus Críticos"
            :value="$stats['critical_count']"
            icon="alert-triangle"
            variant="danger"
        />

        <!-- Pneus em Atenção -->
        <x-ui.stat-card
            title="Atenção"
            :value="$stats['attention_count']"
            icon="alert-circle"
            variant="warning"
        />

        <!-- Vida Útil Média -->
        <x-ui.stat-card
            title="Vida Útil Média"
            :value="number_format($stats['average_lifespan'], 1) . '%'"
            icon="trending-up"
            variant="info"
        />

        <!-- Veículos Monitorados -->
        <x-ui.stat-card
            title="Veículos"
            :value="$stats['total_vehicles']"
            icon="car"
            variant="success"
        />
    </div>

    <!-- Status dos Pneus -->
    <x-ui.card title="Status dos Pneus" subtitle="Distribuição por situação" class="mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['in_use'] }}</p>
                <p class="text-sm text-gray-600 dark:text-navy-400 mt-1">Em Uso</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['in_stock'] }}</p>
                <p class="text-sm text-gray-600 dark:text-navy-400 mt-1">Em Estoque</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['maintenance'] }}</p>
                <p class="text-sm text-gray-600 dark:text-navy-400 mt-1">Manutenção</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-gray-600 dark:text-navy-400">{{ $stats['total'] }}</p>
                <p class="text-sm text-gray-600 dark:text-navy-400 mt-1">Total</p>
            </div>
        </div>
    </x-ui.card>

    <!-- Alertas de Ação Imediata -->
    @if($criticalTires->count() > 0)
        <x-ui.alert-card title="Ação Imediata Necessária" variant="danger" icon="alert-triangle" class="mb-6">
            <p class="mb-4">Os seguintes pneus estão em estado crítico e precisam de substituição urgente:</p>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                    <thead class="bg-gray-50 dark:bg-navy-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Veículo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Pneu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Posição</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Vida Útil</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                        @foreach($criticalTires as $tire)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $tire->vehicle->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-navy-400">
                                        {{ $tire->vehicle->plate ?? 'Sem placa' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $tire->brand }} {{ $tire->model }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    Posição {{ $tire->position ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $percent = ($tire->current_km / $tire->lifespan_km) * 100;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-navy-700 rounded-full h-2 w-24">
                                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs text-red-600 dark:text-red-400 font-medium">{{ number_format($percent, 0) }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('tires.vehicles.show', $tire->vehicle_id) }}"
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                                        Ver Veículo →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.alert-card>
    @endif

    <!-- Pneus em Atenção -->
    @if($attentionTires->count() > 0)
        <x-ui.card title="Pneus em Atenção" subtitle="{{ $attentionTires->count() }} pneus precisam ser monitorados">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                    <thead class="bg-gray-50 dark:bg-navy-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Veículo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Pneu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Posição</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Vida Útil</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                        @foreach($attentionTires as $tire)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $tire->vehicle->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-navy-400">
                                        {{ $tire->vehicle->plate ?? 'Sem placa' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $tire->brand }} {{ $tire->model }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    Posição {{ $tire->position ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $percent = ($tire->current_km / $tire->lifespan_km) * 100;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-navy-700 rounded-full h-2 w-24">
                                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">{{ number_format($percent, 0) }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="{{ route('tires.vehicles.show', $tire->vehicle_id) }}"
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                                        Ver Veículo →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @endif

    <!-- Estado Saudável -->
    @if($criticalTires->count() === 0 && $attentionTires->count() === 0)
        <x-ui.alert-card title="Tudo em Ordem!" variant="success" icon="check-circle">
            <p>Todos os pneus estão em bom estado. Continue monitorando regularmente para garantir a segurança da frota.</p>
        </x-ui.alert-card>
    @endif
</x-app-layout>
