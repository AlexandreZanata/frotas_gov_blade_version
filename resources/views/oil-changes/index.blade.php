<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gerenciamento de Troca de Óleo') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="oilChangeModule()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alertas de Estoque Baixo -->
            @if($lowStockOils->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-800 dark:text-red-300 mb-2">Estoque Baixo de Óleo</h3>
                        <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                            @foreach($lowStockOils as $oil)
                            <li>• {{ $oil->name }} - Estoque: {{ $oil->quantity_on_hand }} {{ $oil->unit_of_measure }} (Nível mínimo: {{ $oil->reorder_level }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Estatísticas -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 border border-gray-200 dark:border-navy-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-600 dark:text-navy-300 uppercase tracking-wide">Total Veículos</div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow p-4 border border-green-200 dark:border-green-800 cursor-pointer hover:shadow-md transition" @click="filterByStatus('em_dia')">
                    <div class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $stats['em_dia'] }}</div>
                    <div class="text-xs text-green-600 dark:text-green-300 uppercase tracking-wide">Em Dia</div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow p-4 border border-yellow-200 dark:border-yellow-800 cursor-pointer hover:shadow-md transition" @click="filterByStatus('atencao')">
                    <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ $stats['atencao'] }}</div>
                    <div class="text-xs text-yellow-600 dark:text-yellow-300 uppercase tracking-wide">Atenção</div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg shadow p-4 border border-orange-200 dark:border-orange-800 cursor-pointer hover:shadow-md transition" @click="filterByStatus('critico')">
                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-400">{{ $stats['critico'] }}</div>
                    <div class="text-xs text-orange-600 dark:text-orange-300 uppercase tracking-wide">Crítico</div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow p-4 border border-red-200 dark:border-red-800 cursor-pointer hover:shadow-md transition" @click="filterByStatus('vencido')">
                    <div class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $stats['vencido'] }}</div>
                    <div class="text-xs text-red-600 dark:text-red-300 uppercase tracking-wide">Vencido</div>
                </div>

                <div class="bg-gray-50 dark:bg-navy-700 rounded-lg shadow p-4 border border-gray-200 dark:border-navy-600 cursor-pointer hover:shadow-md transition" @click="filterByStatus('sem_registro')">
                    <div class="text-2xl font-bold text-gray-700 dark:text-gray-400">{{ $stats['sem_registro'] }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wide">Sem Registro</div>
                </div>
            </div>

            <!-- Busca e Filtros -->
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-4 border border-gray-200 dark:border-navy-700">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text"
                               x-model="searchQuery"
                               @input="filterVehicles"
                               placeholder="Buscar por veículo, placa..."
                               class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <button @click="showRegisterModal = true" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition">
                        + Registrar Troca
                    </button>
                </div>
            </div>

            <!-- Lista de Veículos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($vehicles as $vehicle)
                <div class="bg-white dark:bg-navy-800 rounded-lg shadow border border-gray-200 dark:border-navy-700 p-4 hover:shadow-lg transition"
                     x-show="vehicleMatchesFilter({{ json_encode([
                         'name' => $vehicle->name,
                         'plate' => $vehicle->plate,
                         'status' => $vehicle->oil_status
                     ]) }})"
                     x-transition>

                    <!-- Status Badge -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $vehicle->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-navy-300">{{ $vehicle->plate }}</p>
                        </div>
                        @php
                            $statusConfig = [
                                'em_dia' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-800 dark:text-green-300', 'label' => 'Em Dia'],
                                'atencao' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-800 dark:text-yellow-300', 'label' => 'Atenção'],
                                'critico' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-800 dark:text-orange-300', 'label' => 'Crítico'],
                                'vencido' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-800 dark:text-red-300', 'label' => 'Vencido'],
                                'sem_registro' => ['bg' => 'bg-gray-100 dark:bg-navy-700', 'text' => 'text-gray-800 dark:text-gray-300', 'label' => 'Sem Registro'],
                            ];
                            $config = $statusConfig[$vehicle->oil_status] ?? $statusConfig['sem_registro'];
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ $config['bg'] }} {{ $config['text'] }}">
                            {{ $config['label'] }}
                        </span>
                    </div>

                    @if($vehicle->oil_status !== 'sem_registro' && $vehicle->last_oil_change)
                    <!-- Informações da Última Troca -->
                    <div class="text-sm text-gray-600 dark:text-navy-300 space-y-1 mb-3">
                        <p>Última troca: {{ $vehicle->last_oil_change->change_date->format('d/m/Y') }}</p>
                        <p>KM na troca: {{ number_format($vehicle->last_oil_change->km_at_change, 0, ',', '.') }} km</p>
                    </div>

                    <!-- Barras de Progresso -->
                    <div class="space-y-3">
                        <!-- Progresso KM -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 dark:text-navy-300 mb-1">
                                <span>Quilometragem</span>
                                <span>{{ number_format($vehicle->current_km, 0, ',', '.') }} / {{ number_format($vehicle->last_oil_change->next_change_km, 0, ',', '.') }} km</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-navy-700 rounded-full h-2">
                                @php
                                    $kmProgress = min(100, $vehicle->km_progress);
                                    $kmColorClass = $kmProgress >= 100 ? 'bg-red-600' : ($kmProgress >= 90 ? 'bg-orange-500' : ($kmProgress >= 75 ? 'bg-yellow-500' : 'bg-green-500'));
                                @endphp
                                <div class="h-2 rounded-full {{ $kmColorClass }}" style="width: {{ $kmProgress }}%"></div>
                            </div>
                        </div>

                        <!-- Progresso Tempo -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 dark:text-navy-300 mb-1">
                                <span>Tempo</span>
                                <span>{{ $vehicle->last_oil_change->change_date->diffInDays(now()) }} / {{ $vehicle->last_oil_change->change_date->diffInDays($vehicle->last_oil_change->next_change_date) }} dias</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-navy-700 rounded-full h-2">
                                @php
                                    $dateProgress = min(100, $vehicle->date_progress);
                                    $dateColorClass = $dateProgress >= 100 ? 'bg-red-600' : ($dateProgress >= 90 ? 'bg-orange-500' : ($dateProgress >= 75 ? 'bg-yellow-500' : 'bg-green-500'));
                                @endphp
                                <div class="h-2 rounded-full {{ $dateColorClass }}" style="width: {{ $dateProgress }}%"></div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-sm text-gray-500 dark:text-navy-400 text-center py-4">
                        Nenhuma troca de óleo registrada
                    </div>
                    @endif

                    <!-- Ações -->
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-navy-700">
                        <button @click="openRegisterModal('{{ $vehicle->id }}')" class="flex-1 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-md font-medium transition">
                            Registrar Troca
                        </button>
                        <a href="{{ route('oil-changes.history', $vehicle->id) }}" class="flex-1 px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white text-sm rounded-md font-medium transition text-center">
                            Histórico
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Modal de Registro de Troca -->
        <div x-show="showRegisterModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showRegisterModal = false"></div>

                <div class="relative bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Registrar Troca de Óleo</h3>
                        <button @click="showRegisterModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('oil-changes.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Veículo</label>
                            <select name="vehicle_id" x-model="selectedVehicleId" @change="loadVehicleData" required class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Selecione um veículo</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ $vehicle->plate }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da Troca</label>
                                <input type="date" name="change_date" required class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">KM na Troca</label>
                                <input type="number" name="km_at_change" required class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Óleo (Estoque)</label>
                                <select name="inventory_item_id" x-model="selectedOilId" @change="updateOilInfo" class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">Selecione (opcional)</option>
                                    @foreach(\App\Models\InventoryItem::whereHas('category', function($q) { $q->where('name', 'LIKE', '%óleo%')->orWhere('name', 'LIKE', '%oil%'); })->get() as $oil)
                                    <option value="{{ $oil->id }}" data-stock="{{ $oil->quantity_on_hand }}" data-unit="{{ $oil->unit_of_measure }}" data-cost="{{ $oil->unit_cost ?? 0 }}">
                                        {{ $oil->name }} (Estoque: {{ $oil->quantity_on_hand }} {{ $oil->unit_of_measure }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Litros Utilizados</label>
                                <input type="number" step="0.01" name="liters_used" x-model="litersUsed" @input="calculateCost" class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custo Total (R$)</label>
                                <input type="number" step="0.01" name="cost" x-model="totalCost" class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prestador de Serviço</label>
                                <input type="text" name="service_provider" class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
                            <h4 class="font-semibold text-primary-900 dark:text-primary-300 mb-2">Próxima Troca</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Próxima Troca (KM)</label>
                                    <input type="number" name="next_change_km" required class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Próxima Troca (Data)</label>
                                    <input type="date" name="next_change_date" required class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                            <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                            <button type="button" @click="showRegisterModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white rounded-md font-medium transition">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition">
                                Registrar Troca
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function oilChangeModule() {
            return {
                showRegisterModal: false,
                searchQuery: '',
                statusFilter: '',
                selectedVehicleId: '',
                selectedOilId: '',
                litersUsed: '',
                totalCost: '',
                unitCost: 0,

                openRegisterModal(vehicleId = '') {
                    this.selectedVehicleId = vehicleId;
                    this.showRegisterModal = true;
                    if (vehicleId) {
                        this.$nextTick(() => this.loadVehicleData());
                    }
                },

                filterByStatus(status) {
                    this.statusFilter = this.statusFilter === status ? '' : status;
                    window.location.href = this.statusFilter ? '?status=' + this.statusFilter : window.location.pathname;
                },

                vehicleMatchesFilter(vehicle) {
                    const matchesSearch = !this.searchQuery ||
                        vehicle.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        vehicle.plate.toLowerCase().includes(this.searchQuery.toLowerCase());

                    const matchesStatus = !this.statusFilter || vehicle.status === this.statusFilter;

                    return matchesSearch && matchesStatus;
                },

                updateOilInfo() {
                    const select = event.target;
                    const option = select.options[select.selectedIndex];
                    if (option && option.value) {
                        this.unitCost = parseFloat(option.dataset.cost || 0);
                        this.calculateCost();
                    }
                },

                calculateCost() {
                    if (this.litersUsed && this.unitCost) {
                        this.totalCost = (parseFloat(this.litersUsed) * this.unitCost).toFixed(2);
                    }
                },

                loadVehicleData() {
                    if (!this.selectedVehicleId) return;

                    fetch(`/api/oil-changes/vehicle-data/${this.selectedVehicleId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Auto-preencher campos sugeridos
                            console.log('Vehicle data loaded:', data);
                        });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

