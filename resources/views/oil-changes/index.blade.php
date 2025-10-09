<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Troca de Óleo"
            subtitle="Dashboard de gerenciamento e monitoramento da manutenção preventiva"
            hide-title-mobile
            icon="droplet"
        />
    </x-slot>

    <x-slot name="pageActions">
        <button
            @click="showRegisterModal = true"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Registrar Troca</span>
        </button>
    </x-slot>

    <!-- Definir função JavaScript ANTES do Alpine.js inicializar -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('oilChangeModule', () => ({
                showRegisterModal: false,
                searchQuery: '',
                statusFilter: '{{ request("status", "") }}',
                selectedVehicleId: '',
                selectedOilId: '',
                litersUsed: '',
                totalCost: '',
                unitCost: 0,

                init() {
                    // Listener para abrir modal com veículo pré-selecionado
                    window.addEventListener('open-register-modal', (event) => {
                        this.openRegisterModal(event.detail?.vehicleId);
                    });
                },

                openRegisterModal(vehicleId = '') {
                    this.selectedVehicleId = vehicleId;
                    this.showRegisterModal = true;
                    if (vehicleId) {
                        this.$nextTick(() => this.loadVehicleData());
                    }
                },

                filterByStatus(status) {
                    if (this.statusFilter === status) {
                        window.location.href = '{{ route("oil-changes.index") }}';
                    } else {
                        window.location.href = '{{ route("oil-changes.index") }}?status=' + status;
                    }
                },

                vehicleMatchesFilter(vehicle) {
                    // Filtro de busca
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase();
                        const matchesName = vehicle.name.toLowerCase().includes(query);
                        const matchesPlate = vehicle.plate.toLowerCase().includes(query);

                        if (!matchesName && !matchesPlate) {
                            return false;
                        }
                    }

                    return true;
                },

                filterVehicles() {
                    // Filtro em tempo real, sem reload
                },

                loadVehicleData() {
                    if (!this.selectedVehicleId) return;

                    // Buscar dados do veículo via API
                    fetch(`/api/oil-changes/vehicle-data/${this.selectedVehicleId}`)
                        .then(response => response.json())
                        .then(data => {
                            const kmAtChangeInput = document.getElementById('km_at_change');
                            const nextChangeKmInput = document.getElementById('next_change_km');
                            const nextChangeDateInput = document.getElementById('next_change_date');
                            const litersUsedInput = document.getElementById('liters_used');
                            const changeDateInput = document.getElementById('change_date');

                            // Preencher KM atual do veículo
                            if (data.current_km && kmAtChangeInput) {
                                kmAtChangeInput.value = data.current_km;
                            }

                            // Calcular e preencher PRÓXIMA TROCA KM automaticamente
                            if (nextChangeKmInput) {
                                let baseKm = 0;

                                if (data.last_oil_change) {
                                    // Se já houve troca, usar o KM da última troca
                                    baseKm = data.last_oil_change.next_change_km ||
                                        (data.last_oil_change.km_at_change + data.suggested_km_interval);
                                } else if (kmAtChangeInput.value) {
                                    // Se é primeira troca, usar KM atual + intervalo
                                    baseKm = parseInt(kmAtChangeInput.value) + data.suggested_km_interval;
                                } else if (data.current_km) {
                                    // Fallback: usar KM atual do veículo + intervalo
                                    baseKm = parseInt(data.current_km) + data.suggested_km_interval;
                                }

                                nextChangeKmInput.value = baseKm;
                            }

                            // Calcular e preencher PRÓXIMA TROCA DATA automaticamente
                            if (nextChangeDateInput) {
                                const baseDate = changeDateInput && changeDateInput.value
                                    ? new Date(changeDateInput.value)
                                    : new Date();

                                baseDate.setDate(baseDate.getDate() + data.suggested_days_interval);
                                const suggestedDate = baseDate.toISOString().split('T')[0];
                                nextChangeDateInput.value = suggestedDate;
                            }

                            // Sugerir litros padrão se configurado
                            if (data.suggested_liters && litersUsedInput) {
                                this.litersUsed = data.suggested_liters;
                            }

                            console.log('Dados carregados e calculados:', {
                                currentKm: data.current_km,
                                suggestedKmInterval: data.suggested_km_interval,
                                suggestedDaysInterval: data.suggested_days_interval,
                                nextChangeKm: nextChangeKmInput?.value,
                                nextChangeDate: nextChangeDateInput?.value
                            });
                        })
                        .catch(error => {
                            console.error('Erro ao carregar dados do veículo:', error);
                        });
                },

                // Método auxiliar para recalcular a data da próxima troca quando mudar a data da troca
                recalculateNextChangeDate() {
                    const changeDateInput = document.getElementById('change_date');
                    const nextChangeDateInput = document.getElementById('next_change_date');

                    if (changeDateInput && changeDateInput.value && nextChangeDateInput && this.selectedVehicleId) {
                        // Buscar intervalo de dias sugerido
                        fetch(`/api/oil-changes/vehicle-data/${this.selectedVehicleId}`)
                            .then(response => response.json())
                            .then(data => {
                                const baseDate = new Date(changeDateInput.value);
                                baseDate.setDate(baseDate.getDate() + data.suggested_days_interval);
                                nextChangeDateInput.value = baseDate.toISOString().split('T')[0];
                            });
                    }
                },

                // Método auxiliar para recalcular o KM da próxima troca quando mudar o KM atual
                recalculateNextChangeKm() {
                    const kmAtChangeInput = document.getElementById('km_at_change');
                    const nextChangeKmInput = document.getElementById('next_change_km');

                    if (kmAtChangeInput && kmAtChangeInput.value && nextChangeKmInput && this.selectedVehicleId) {
                        // Buscar intervalo de KM sugerido
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

    <!-- Wrap everything in x-data to share Alpine.js state -->
    <div x-data="oilChangeModule()">
        <!-- Alertas de Estoque Baixo -->
        @if($lowStockOils->count() > 0)
            <x-ui.alert-card title="Estoque Baixo de Óleo" variant="danger" icon="alert-triangle">
                <ul class="space-y-1">
                    @foreach($lowStockOils as $oil)
                        <li class="flex items-start gap-2">
                            <span class="text-red-600 dark:text-red-400 mt-0.5">•</span>
                            <span><strong>{{ $oil->name }}</strong> - Estoque: {{ $oil->quantity_on_hand }} {{ $oil->unit_of_measure }} (Mínimo: {{ $oil->reorder_level }})</span>
                        </li>
                    @endforeach
                </ul>
            </x-ui.alert-card>
        @endif

        <!-- Estatísticas Gerais -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <x-ui.stat-card
                title="Total de Veículos"
                :value="$stats['total']"
                variant="default"
                icon="car"
            />

            <x-ui.stat-card
                title="Em Dia"
                :value="$stats['em_dia']"
                variant="success"
                icon="check-circle"
                clickable
                :href="route('oil-changes.index', ['status' => 'em_dia'])"
                x-on:click.prevent="filterByStatus('em_dia')"
            />

            <x-ui.stat-card
                title="Atenção"
                :value="$stats['atencao']"
                variant="warning"
                icon="alert-circle"
                clickable
                :href="route('oil-changes.index', ['status' => 'atencao'])"
                x-on:click.prevent="filterByStatus('atencao')"
            />

            <x-ui.stat-card
                title="Crítico"
                :value="$stats['critico']"
                variant="orange"
                icon="alert-triangle"
                clickable
                :href="route('oil-changes.index', ['status' => 'critico'])"
                x-on:click.prevent="filterByStatus('critico')"
            />

            <x-ui.stat-card
                title="Vencido"
                :value="$stats['vencido']"
                variant="danger"
                icon="x-circle"
                clickable
                :href="route('oil-changes.index', ['status' => 'vencido'])"
                x-on:click.prevent="filterByStatus('vencido')"
            />

            <x-ui.stat-card
                title="Sem Registro"
                :value="$stats['sem_registro']"
                variant="gray"
                icon="file-minus"
                clickable
                :href="route('oil-changes.index', ['status' => 'sem_registro'])"
                x-on:click.prevent="filterByStatus('sem_registro')"
            />
        </div>

        <!-- Busca e Filtros -->
        <x-ui.card padding="p-4">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="search" class="h-4 w-4 text-gray-400 dark:text-navy-400" />
                    </div>
                    <input
                        type="text"
                        x-model="searchQuery"
                        @input="filterVehicles"
                        placeholder="Buscar por veículo, placa..."
                        class="w-full pl-10 rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                @if(request('status'))
                    <a href="{{ route('oil-changes.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white rounded-md font-medium transition">
                        <x-icon name="x" class="w-4 h-4" />
                        <span>Limpar Filtro</span>
                    </a>
                @endif
            </div>
        </x-ui.card>

        <!-- Lista de Veículos em Grid de Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($vehicles as $vehicle)
                <div x-show="vehicleMatchesFilter({{ json_encode([
                        'name' => $vehicle->name,
                        'plate' => $vehicle->plate,
                        'status' => $vehicle->oil_status
                    ]) }})"
                     x-transition>
                    <x-ui.oil-vehicle-card
                        :vehicle="$vehicle"
                        :last-oil-change="$vehicle->last_oil_change ?? null"
                        :status="$vehicle->oil_status"
                        :km-progress="$vehicle->km_progress"
                        :date-progress="$vehicle->date_progress"
                        :current-km="$vehicle->current_km"
                    />
                </div>
            @empty
                <div class="col-span-full">
                    <x-ui.card padding="p-8">
                        <div class="text-center text-gray-500 dark:text-navy-400">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="text-lg font-medium mb-1">Nenhum veículo encontrado</h3>
                            <p class="text-sm">Não há veículos cadastrados no sistema.</p>
                        </div>
                    </x-ui.card>
                </div>
            @endforelse
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
                            <x-icon name="x" class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Formulário -->
                    <form method="POST" action="{{ route('oil-changes.store') }}" class="p-6">
                        @csrf

                        <!-- Seleção de Veículo -->
                        <div class="mb-6">
                            <x-input-label for="vehicle_id" value="Veículo *" />
                            <select
                                name="vehicle_id"
                                id="vehicle_id"
                                x-model="selectedVehicleId"
                                @change="loadVehicleData"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Selecione um veículo</option>
                                @foreach($allVehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }} - {{ $v->plate }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Informações da Troca -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <x-icon name="calendar" class="w-4 h-4" />
                                Informações da Troca
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="change_date" value="Data da Troca *" />
                                    <input
                                        type="date"
                                        name="change_date"
                                        id="change_date"
                                        required
                                        value="{{ date('Y-m-d') }}"
                                        max="{{ date('Y-m-d') }}"
                                        @change="recalculateNextChangeDate"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                                <div>
                                    <x-input-label for="km_at_change" value="Quilometragem na Troca *" />
                                    <input
                                        type="number"
                                        name="km_at_change"
                                        id="km_at_change"
                                        required
                                        min="0"
                                        step="1"
                                        placeholder="Ex: 15000"
                                        @input="recalculateNextChangeKm"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                            </div>
                        </div>

                        <!-- Óleo e Estoque -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <x-icon name="droplet" class="w-4 h-4" />
                                Óleo Utilizado
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="inventory_item_id" value="Tipo de Óleo (Estoque)" />
                                    <select
                                        name="inventory_item_id"
                                        id="inventory_item_id"
                                        x-model="selectedOilId"
                                        @change="updateOilInfo"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
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
                                    <x-input-label for="liters_used" value="Litros Utilizados" />
                                    <input
                                        type="number"
                                        name="liters_used"
                                        id="liters_used"
                                        x-model="litersUsed"
                                        @input="calculateCost"
                                        step="0.1"
                                        min="0"
                                        placeholder="Ex: 4.5"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                            </div>
                        </div>

                        <!-- Custo e Prestador -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <x-icon name="dollar-sign" class="w-4 h-4" />
                                Informações Financeiras
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="cost" value="Custo Total (R$)" />
                                    <input
                                        type="number"
                                        name="cost"
                                        id="cost"
                                        x-model="totalCost"
                                        step="0.01"
                                        min="0"
                                        placeholder="Ex: 150.00"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                                <div>
                                    <x-input-label for="service_provider" value="Prestador de Serviço" />
                                    <input
                                        type="text"
                                        name="service_provider"
                                        id="service_provider"
                                        placeholder="Ex: Oficina ABC"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                            </div>
                        </div>

                        <!-- Próxima Troca -->
                        <div class="mb-6 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-primary-900 dark:text-primary-300 mb-3 flex items-center gap-2">
                                <x-icon name="calendar-plus" class="w-4 h-4" />
                                Próxima Troca Prevista
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="next_change_km" value="Próxima Troca (KM) *" class="text-primary-800 dark:text-primary-200" />
                                    <input
                                        type="number"
                                        name="next_change_km"
                                        id="next_change_km"
                                        required
                                        min="0"
                                        step="1"
                                        placeholder="Ex: 25000"
                                        class="mt-1 block w-full rounded-md border-primary-300 dark:border-primary-700 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <p class="mt-1 text-xs text-primary-700 dark:text-primary-400">
                                        Geralmente + 10.000 km
                                    </p>
                                </div>
                                <div>
                                    <x-input-label for="next_change_date" value="Próxima Troca (Data) *" class="text-primary-800 dark:text-primary-200" />
                                    <input
                                        type="date"
                                        name="next_change_date"
                                        id="next_change_date"
                                        required
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        class="mt-1 block w-full rounded-md border-primary-300 dark:border-primary-700 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <p class="mt-1 text-xs text-primary-700 dark:text-primary-400">
                                        Geralmente + 6 meses
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="mb-6">
                            <x-input-label for="notes" value="Observações" />
                            <textarea
                                name="notes"
                                id="notes"
                                rows="3"
                                placeholder="Informações adicionais sobre a troca de óleo..."
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>

                        <!-- Ações do Formulário -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                            <button
                                type="button"
                                @click="showRegisterModal = false"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white rounded-md font-medium transition">
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition inline-flex items-center gap-2">
                                <x-icon name="check" class="w-4 h-4" />
                                <span>Registrar Troca</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
