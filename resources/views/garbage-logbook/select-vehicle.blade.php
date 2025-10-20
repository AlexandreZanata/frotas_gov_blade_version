<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Coleta - Selecionar Veículo" subtitle="Escolha o veículo para iniciar uma coleta" hide-title-mobile icon="truck" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('garbage-logbook.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <x-ui.card title="Selecione o Veículo" subtitle="Pesquise e escolha o veículo que você irá utilizar nesta coleta">
            {{-- Passa os dados dos veículos para o Alpine.js --}}
            <form action="{{ route('garbage-logbook.store-vehicle') }}" method="POST" x-data="vehicleSelectData({{ json_encode($vehicles) }})">
                @csrf

                <div class="space-y-6">
                    <div>
                        <x-input-label for="vehicle_id" value="Selecionar Veículo *" />
                        <x-ui.select name="vehicle_id" id="vehicle_id" class="mt-2" required x-model="selectedVehicleId" @change="updateVehicleInfo()">
                            <option value="">Selecione um veículo...</option>
                            {{-- Loop para criar as opções (usando sintaxe Alpine) --}}
                            <template x-for="vehicle in vehicles" :key="vehicle.id">
                                <option :value="vehicle.id"
                                        :disabled="!vehicle.available"
                                        x-text="`${vehicle.prefix} - ${vehicle.name} (${vehicle.plate}) - ${vehicle.available ? 'Disponível' : 'Em uso'}`">
                                </option>
                            </template>
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                    </div>

                    <div id="vehicle-info" x-show="selectedVehicle" x-transition class="p-4 bg-gray-50 dark:bg-navy-700 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-600 dark:text-navy-300">Placa:</span>
                                <span id="vehicle-plate" class="ml-2 text-gray-900 dark:text-navy-50" x-text="selectedVehicle?.plate || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600 dark:text-navy-300">Secretaria:</span>
                                <span id="vehicle-secretariat" class="ml-2 text-gray-900 dark:text-navy-50" x-text="selectedVehicle?.secretariat || 'N/A'"></span>
                            </div>
                            <div class="md:col-span-2">
                                <span class="text-sm font-medium text-gray-600 dark:text-navy-300">Status:</span>
                                <span id="vehicle-status" class="ml-2">
                                    {{-- O status é atualizado via JS --}}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('garbage-logbook.index') }}">
                            <x-secondary-button type="button">
                                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                                Cancelar
                            </x-secondary-button>
                        </a>

                        {{--
                            CORREÇÃO AQUI:
                            :disabled (interpretado pelo Blade/PHP)
                            foi trocado por
                            x-bind:disabled (interpretado pelo Alpine/JS)
                        --}}
                        <x-primary-button type="submit" id="submit-btn" x-bind:disabled="!selectedVehicle || !selectedVehicle.available">
                            Continuar
                            <x-icon name="chevron-right" class="w-4 h-4 ml-2" />
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </x-ui.card>
    </div>

    @push('scripts')
        <script>
            // Lógica Alpine.js para gerenciar o estado do select e das informações
            function vehicleSelectData(vehiclesData) {
                return {
                    vehicles: vehiclesData, // Array de veículos vindo do PHP
                    selectedVehicleId: '{{ old('vehicle_id', '') }}', // ID selecionado (ou valor antigo)
                    selectedVehicle: null, // Objeto do veículo selecionado

                    init() {
                        // Define o veículo inicial se houver um ID selecionado (ex: erro de validação)
                        if (this.selectedVehicleId) {
                            this.updateVehicleInfo();
                        } else {
                            // Garante que o botão esteja desabilitado no início se nada estiver selecionado
                            this.updateStatusDisplay();
                        }
                    },

                    updateVehicleInfo() {
                        if (!this.selectedVehicleId) {
                            this.selectedVehicle = null;
                            this.updateStatusDisplay(); // Limpa o status visual
                            return;
                        }

                        // Encontra o objeto do veículo no array com base no ID selecionado
                        this.selectedVehicle = this.vehicles.find(v => v.id === this.selectedVehicleId);
                        this.updateStatusDisplay(); // Atualiza o status visual
                    },

                    updateStatusDisplay() {
                        const vehicleStatusElement = document.getElementById('vehicle-status');
                        if (!vehicleStatusElement) return;

                        if (this.selectedVehicle) {
                            if (this.selectedVehicle.available) {
                                vehicleStatusElement.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Disponível</span>`;
                            } else {
                                vehicleStatusElement.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Em uso</span>`;
                            }
                        } else {
                            vehicleStatusElement.innerHTML = ''; // Limpa se nada selecionado
                        }

                        // Atualiza estado do botão submit
                        const submitBtn = document.getElementById('submit-btn');
                        if (submitBtn) {
                            submitBtn.disabled = !this.selectedVehicle || !this.selectedVehicle.available;
                        }
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
