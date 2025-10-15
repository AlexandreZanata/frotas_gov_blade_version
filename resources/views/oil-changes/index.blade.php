<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Troca de Óleo"
            subtitle="Gerenciamento e monitoramento da manutenção preventiva"
            hide-title-mobile
            icon="droplet"
        />
    </x-slot>

    <x-slot name="pageActions">
        <button
            @click="showRegisterModal = true"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Registrar Troca</span>
        </button>
    </x-slot>

    <div x-data="oilChangeModule()" class="space-y-6">
        <!-- Alertas de Estoque Baixo -->
        @if($lowStockOils->isNotEmpty())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div class="text-sm text-red-800 dark:text-red-300">
                        <p class="font-semibold mb-1">Estoque Baixo de Óleo</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($lowStockOils as $oil)
                                <li><strong>{{ $oil->name }}</strong> - Estoque: {{ $oil->quantity_on_hand }} {{ $oil->unit_of_measure }} (Mínimo: {{ $oil->reorder_level }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Estatísticas -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 text-center cursor-pointer hover:shadow-md transition"
                 onclick="window.location='{{ route('oil-changes.index') }}'">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500 dark:text-navy-300 mt-1">Total</div>
            </div>
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 text-center cursor-pointer hover:shadow-md transition"
                 onclick="window.location='{{ route('oil-changes.index', ['status' => 'em_dia']) }}'">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['em_dia'] }}</div>
                <div class="text-sm text-gray-500 dark:text-navy-300 mt-1">Em Dia</div>
            </div>
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 text-center cursor-pointer hover:shadow-md transition"
                 onclick="window.location='{{ route('oil-changes.index', ['status' => 'atencao']) }}'">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['atencao'] }}</div>
                <div class="text-sm text-gray-500 dark:text-navy-300 mt-1">Atenção</div>
            </div>
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 text-center cursor-pointer hover:shadow-md transition"
                 onclick="window.location='{{ route('oil-changes.index', ['status' => 'critico']) }}'">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['critico'] }}</div>
                <div class="text-sm text-gray-500 dark:text-navy-300 mt-1">Crítico</div>
            </div>
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 text-center cursor-pointer hover:shadow-md transition"
                 onclick="window.location='{{ route('oil-changes.index', ['status' => 'vencido']) }}'">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['vencido'] }}</div>
                <div class="text-sm text-gray-500 dark:text-navy-300 mt-1">Vencido</div>
            </div>
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 text-center cursor-pointer hover:shadow-md transition"
                 onclick="window.location='{{ route('oil-changes.index', ['status' => 'sem_registro']) }}'">
                <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['sem_registro'] }}</div>
                <div class="text-sm text-gray-500 dark:text-navy-300 mt-1">Sem Registro</div>
            </div>
        </div>

        <!-- Tabela de Veículos -->
        <div class="bg-white dark:bg-navy-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Veículos</h3>
            </div>
            <div class="p-6">
                <!-- Barra de Pesquisa -->
                <div class="mb-4">
                    <form method="GET" class="flex gap-2">
                        <input type="text"
                               name="search"
                               value="{{ request('search', '') }}"
                               placeholder="Buscar por nome ou placa..."
                               class="flex-1 rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <button type="submit"
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                            Buscar
                        </button>
                    </form>
                </div>

                <!-- Tabela -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                        <thead class="bg-gray-50 dark:bg-navy-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Veículo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Última Troca</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Próxima Troca</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Progresso</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Ações</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                        @forelse($vehicles as $vehicle)
                            <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                                <!-- Veículo -->
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $vehicle->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-navy-400">{{ $vehicle->plate }}</div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    @php
                                        $statusClasses = [
                                            'em_dia' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                            'atencao' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                            'critico' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                            'vencido' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            'sem_registro' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
                                        ];
                                        $statusText = [
                                            'em_dia' => 'Em Dia',
                                            'atencao' => 'Atenção',
                                            'critico' => 'Crítico',
                                            'vencido' => 'Vencido',
                                            'sem_registro' => 'Sem Registro'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$vehicle->oil_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusText[$vehicle->oil_status] ?? 'Desconhecido' }}
                                        </span>
                                </td>

                                <!-- Última Troca -->
                                <td class="px-4 py-3 text-sm">
                                    @if($vehicle->last_oil_change)
                                        <div>{{ $vehicle->last_oil_change->change_date->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($vehicle->last_oil_change->km_at_change, 0, ',', '.') }} km</div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <!-- Próxima Troca -->
                                <td class="px-4 py-3 text-sm">
                                    @if($vehicle->last_oil_change)
                                        <div>{{ $vehicle->last_oil_change->next_change_date->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($vehicle->last_oil_change->next_change_km, 0, ',', '.') }} km</div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <!-- Progresso -->
                                <td class="px-4 py-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs w-8 text-gray-500">KM</span>
                                            <div class="flex-1 bg-gray-200 dark:bg-navy-700 rounded-full h-1.5 w-24">
                                                <div class="{{ $vehicle->km_progress_color }} h-1.5 rounded-full" style="width: {{ $vehicle->km_progress }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs w-8 text-gray-500">Data</span>
                                            <div class="flex-1 bg-gray-200 dark:bg-navy-700 rounded-full h-1.5 w-24">
                                                <div class="{{ $vehicle->date_progress_color }} h-1.5 rounded-full" style="width: {{ $vehicle->date_progress }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Ações -->
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openRegisterModal('{{ $vehicle->id }}')"
                                                title="Registrar Troca"
                                                class="p-1.5 text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/20 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                        <a href="{{ route('oil-changes.history', $vehicle) }}"
                                           title="Histórico"
                                           class="p-1.5 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                                    Nenhum veículo encontrado para os filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($vehicles->hasPages())
                    <div class="mt-4">
                        {{ $vehicles->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal de Registro de Troca -->
        <div x-show="showRegisterModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                     @click="showRegisterModal = false"></div>

                <div class="relative bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                    <!-- Cabeçalho do Modal -->
                    <div class="sticky top-0 bg-white dark:bg-navy-800 border-b border-gray-200 dark:border-navy-700 px-6 py-4 flex justify-between items-center z-10">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Registrar Troca de Óleo</h3>
                            <p class="text-sm text-gray-500 dark:text-navy-300 mt-0.5">Preencha os dados da manutenção realizada</p>
                        </div>
                        <button @click="showRegisterModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Formulário -->
                    <form id="oil-change-form" method="POST" action="{{ route('oil-changes.store') }}" class="p-6 space-y-6">
                        @csrf

                        <!-- Seleção de Veículo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                Veículo <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="vehicle_id"
                                id="vehicle_id"
                                x-model="selectedVehicleId"
                                @change="loadVehicleData"
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Selecione um veículo</option>
                                @foreach($allVehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }} - {{ $v->plate }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Informações da Troca -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-navy-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Informações da Troca
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                        Data da Troca <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        name="change_date"
                                        id="change_date"
                                        required
                                        value="{{ date('Y-m-d') }}"
                                        max="{{ date('Y-m-d') }}"
                                        @change="recalculateNextChangeDate"
                                        class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                        Quilometragem na Troca <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="km_at_change"
                                        id="km_at_change"
                                        required
                                        min="0"
                                        step="1"
                                        placeholder="Ex: 15000"
                                        @input="recalculateNextChangeKm"
                                        class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                        </div>

                        <!-- Óleo e Estoque -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-navy-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                                Óleo Utilizado
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">Tipo de Óleo (Estoque)</label>
                                    <select
                                        name="inventory_item_id"
                                        id="inventory_item_id"
                                        x-model="selectedOilId"
                                        @change="updateOilInfo"
                                        class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Selecione (opcional)</option>
                                        @foreach($oilItems as $oil)
                                            <option
                                                value="{{ $oil->id }}"
                                                data-stock="{{ $oil->quantity_on_hand }}"
                                                data-unit="{{ $oil->unit_of_measure }}"
                                                data-cost="{{ $oil->unit_cost ?? 0 }}">
                                                {{ $oil->name }} (Estoque: {{ $oil->quantity_on_hand }} {{ $oil->unit_of_measure }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">
                                        Selecione para dar baixa no estoque automaticamente
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">Litros Utilizados</label>
                                    <input
                                        type="number"
                                        name="liters_used"
                                        id="liters_used"
                                        x-model="litersUsed"
                                        @input="calculateCost"
                                        step="0.1"
                                        min="0"
                                        placeholder="Ex: 4.5"
                                        class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                        </div>

                        <!-- Custo e Prestador -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-navy-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                Informações Financeiras
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">Custo Total (R$)</label>
                                    <input
                                        type="number"
                                        name="cost"
                                        id="cost"
                                        x-model="totalCost"
                                        step="0.01"
                                        min="0"
                                        placeholder="Ex: 150.00"
                                        class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">Prestador de Serviço</label>
                                    <input
                                        type="text"
                                        name="service_provider"
                                        id="service_provider"
                                        placeholder="Ex: Oficina ABC"
                                        class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                        </div>

                        <!-- Próxima Troca -->
                        <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-primary-900 dark:text-primary-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Próxima Troca Prevista
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-primary-800 dark:text-primary-200 mb-2">
                                        Próxima Troca (KM) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="next_change_km"
                                        id="next_change_km"
                                        required
                                        min="0"
                                        step="1"
                                        placeholder="Ex: 25000"
                                        class="w-full rounded-lg border-primary-300 dark:border-primary-700 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p class="mt-1 text-xs text-primary-700 dark:text-primary-400">
                                        Geralmente + 10.000 km
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-primary-800 dark:text-primary-200 mb-2">
                                        Próxima Troca (Data) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        name="next_change_date"
                                        id="next_change_date"
                                        required
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        class="w-full rounded-lg border-primary-300 dark:border-primary-700 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p class="mt-1 text-xs text-primary-700 dark:text-primary-400">
                                        Geralmente + 6 meses
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">Observações</label>
                            <textarea
                                name="notes"
                                id="notes"
                                rows="3"
                                placeholder="Informações adicionais sobre a troca de óleo..."
                                class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>

                        <!-- Ações do Formulário -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                            <button
                                type="button"
                                @click="showRegisterModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Registrar Troca</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('oilChangeModule', () => ({
                    showRegisterModal: false,
                    selectedVehicleId: '',
                    selectedOilId: '',
                    litersUsed: '',
                    totalCost: '',
                    unitCost: 0,

                    init() {
                        window.addEventListener('open-register-modal', (event) => {
                            this.openRegisterModal(event.detail?.vehicleId);
                        });
                    },

                    openRegisterModal(vehicleId = '') {
                        // Reseta os campos ao abrir
                        this.selectedVehicleId = vehicleId;
                        this.selectedOilId = '';
                        this.litersUsed = '';
                        this.totalCost = '';
                        this.unitCost = 0;

                        const form = document.getElementById('oil-change-form');
                        if(form) form.reset();

                        this.showRegisterModal = true;

                        if (vehicleId) {
                            this.$nextTick(() => this.loadVehicleData());
                        }
                    },

                    loadVehicleData() {
                        if (!this.selectedVehicleId) return;

                        fetch(`/api/oil-changes/vehicle-data/${this.selectedVehicleId}`)
                            .then(response => response.json())
                            .then(data => {
                                const kmAtChangeInput = document.getElementById('km_at_change');
                                const nextChangeKmInput = document.getElementById('next_change_km');
                                const nextChangeDateInput = document.getElementById('next_change_date');
                                const litersUsedInput = document.getElementById('liters_used');
                                const changeDateInput = document.getElementById('change_date');

                                if (data.current_km && kmAtChangeInput) {
                                    kmAtChangeInput.value = data.current_km;
                                }

                                if (nextChangeKmInput) {
                                    let baseKm = 0;
                                    if (data.last_oil_change) {
                                        baseKm = data.last_oil_change.next_change_km || (data.last_oil_change.km_at_change + data.suggested_km_interval);
                                    } else if (kmAtChangeInput.value) {
                                        baseKm = parseInt(kmAtChangeInput.value) + data.suggested_km_interval;
                                    } else if (data.current_km) {
                                        baseKm = parseInt(data.current_km) + data.suggested_km_interval;
                                    }
                                    nextChangeKmInput.value = baseKm;
                                }

                                if (nextChangeDateInput) {
                                    const baseDate = changeDateInput && changeDateInput.value ? new Date(changeDateInput.value) : new Date();
                                    baseDate.setDate(baseDate.getDate() + data.suggested_days_interval);
                                    const suggestedDate = baseDate.toISOString().split('T')[0];
                                    nextChangeDateInput.value = suggestedDate;
                                }

                                if (data.suggested_liters && litersUsedInput) {
                                    this.litersUsed = data.suggested_liters;
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao carregar dados do veículo:', error);
                            });
                    },

                    recalculateNextChangeDate() {
                        const changeDateInput = document.getElementById('change_date');
                        const nextChangeDateInput = document.getElementById('next_change_date');
                        if (changeDateInput && changeDateInput.value && nextChangeDateInput && this.selectedVehicleId) {
                            fetch(`/api/oil-changes/vehicle-data/${this.selectedVehicleId}`)
                                .then(response => response.json())
                                .then(data => {
                                    const baseDate = new Date(changeDateInput.value);
                                    baseDate.setDate(baseDate.getDate() + data.suggested_days_interval);
                                    nextChangeDateInput.value = baseDate.toISOString().split('T')[0];
                                });
                        }
                    },

                    recalculateNextChangeKm() {
                        const kmAtChangeInput = document.getElementById('km_at_change');
                        const nextChangeKmInput = document.getElementById('next_change_km');
                        if (kmAtChangeInput && kmAtChangeInput.value && nextChangeKmInput && this.selectedVehicleId) {
                            fetch(`/api/oil-changes/vehicle-data/${this.selectedVehicleId}`)
                                .then(response => response.json())
                                .then(data => {
                                    const currentKm = parseInt(kmAtChangeInput.value);
                                    nextChangeKmInput.value = currentKm + data.suggested_km_interval;
                                });
                        }
                    },

                    updateOilInfo() {
                        if (!this.selectedOilId) {
                            this.unitCost = 0;
                            return;
                        }
                        const select = document.getElementById('inventory_item_id');
                        const option = select.options[select.selectedIndex];
                        this.unitCost = parseFloat(option.dataset.cost) || 0;
                        this.calculateCost();
                    },

                    calculateCost() {
                        if (this.litersUsed && this.unitCost > 0) {
                            this.totalCost = (parseFloat(this.litersUsed) * this.unitCost).toFixed(2);
                        }
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>
