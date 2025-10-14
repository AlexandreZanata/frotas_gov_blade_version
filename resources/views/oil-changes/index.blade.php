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
            <x-icon name="plus" class="w-4 h-4" />
            <span>Registrar Troca</span>
        </button>
    </x-slot>

    <!-- Lógica Alpine.js para o Modal e Ações -->
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

    <div x-data="oilChangeModule()">
        <!-- Alertas de Estoque Baixo -->
        @if($lowStockOils->isNotEmpty())
            <x-ui.alert-card title="Estoque Baixo de Óleo" variant="danger" icon="alert-triangle" class="mb-6">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach($lowStockOils as $oil)
                        <li><strong>{{ $oil->name }}</strong> - Estoque: {{ $oil->quantity_on_hand }} {{ $oil->unit_of_measure }} (Mínimo: {{ $oil->reorder_level }})</li>
                    @endforeach
                </ul>
            </x-ui.alert-card>
        @endif

        <!-- Estatísticas (Filtros) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <x-ui.stat-card title="Total" :value="$stats['total']" variant="default" icon="car" :href="route('oil-changes.index')" :active="!request('status')"/>
            <x-ui.stat-card title="Em Dia" :value="$stats['em_dia']" variant="success" icon="check-circle" :href="route('oil-changes.index', ['status' => 'em_dia'])" :active="request('status') == 'em_dia'"/>
            <x-ui.stat-card title="Atenção" :value="$stats['atencao']" variant="warning" icon="alert-circle" :href="route('oil-changes.index', ['status' => 'atencao'])" :active="request('status') == 'atencao'"/>
            <x-ui.stat-card title="Crítico" :value="$stats['critico']" variant="orange" icon="alert-triangle" :href="route('oil-changes.index', ['status' => 'critico'])" :active="request('status') == 'critico'"/>
            <x-ui.stat-card title="Vencido" :value="$stats['vencido']" variant="danger" icon="x-circle" :href="route('oil-changes.index', ['status' => 'vencido'])" :active="request('status') == 'vencido'"/>
            <x-ui.stat-card title="Sem Registro" :value="$stats['sem_registro']" variant="gray" icon="file-minus" :href="route('oil-changes.index', ['status' => 'sem_registro'])" :active="request('status') == 'sem_registro'"/>
        </div>

        <!-- Tabela de Veículos -->
        <x-ui.card>
            <x-ui.table
                :headers="['Veículo', 'Status', 'Última Troca', 'Próxima Troca', 'Progresso', 'Ações']"
                :searchable="true"
                search-placeholder="Buscar por nome ou placa..."
                :search-value="request('search', '')"
                :pagination="$vehicles">

                @forelse($vehicles as $vehicle)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                        {{-- Veículo --}}
                        <td class="px-4 py-2 font-medium">
                            <div class="text-gray-900 dark:text-white">{{ $vehicle->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-navy-400">{{ $vehicle->plate }}</div>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-2">
                            <x-ui.oil-status-badge :status="$vehicle->oil_status" />
                        </td>

                        {{-- Última Troca --}}
                        <td class="px-4 py-2 text-sm">
                            @if($vehicle->last_oil_change)
                                <div>{{ $vehicle->last_oil_change->change_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($vehicle->last_oil_change->km_at_change, 0, ',', '.') }} km</div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Próxima Troca --}}
                        <td class="px-4 py-2 text-sm">
                            @if($vehicle->last_oil_change)
                                <div>{{ $vehicle->last_oil_change->next_change_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($vehicle->last_oil_change->next_change_km, 0, ',', '.') }} km</div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Progresso --}}
                        <td class="px-4 py-2">
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

                        {{-- Ações --}}
                        <td class="px-4 py-2 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="openRegisterModal('{{ $vehicle->id }}')" title="Registrar Troca" class="p-1.5 rounded-md text-green-600 bg-green-100 hover:bg-green-200 dark:text-green-300 dark:bg-green-800/50 dark:hover:bg-green-800">
                                    <x-icon name="plus" class="w-4 h-4" />
                                </button>
                                <x-ui.action-icon :href="route('oil-changes.history', $vehicle)" icon="list" title="Histórico" variant="neutral" />
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
            </x-ui.table>
        </x-ui.card>

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
                    <form id="oil-change-form" method="POST" action="{{ route('oil-changes.store') }}" class="p-6">
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
