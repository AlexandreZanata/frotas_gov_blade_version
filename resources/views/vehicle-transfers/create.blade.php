<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Transferência" subtitle="Solicitar transferência de veículo" hide-title-mobile icon="swap" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-transfers.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações da Transferência">
        <form action="{{ route('vehicle-transfers.store') }}" method="POST" class="space-y-6"
            x-data="{
                vehicleSearch: '',
                vehicleResults: [],
                vehicleLoading: false,
                showVehicleDropdown: false,
                selectedVehicle: null,
                selectedVehicleId: '',
                transferType: '{{ old('type') }}',

                async searchVehicle() {
                    if (this.vehicleSearch.length < 2) {
                        this.vehicleResults = [];
                        return;
                    }

                    this.vehicleLoading = true;
                    try {
                        const response = await fetch(`{{ route('api.vehicles.search') }}?q=${encodeURIComponent(this.vehicleSearch)}`);
                        const data = await response.json();
                        this.vehicleResults = data.map(v => ({
                            id: v.id,
                            plate: v.plate,
                            prefix: v.prefix,
                            name: v.name,
                            secretariat_name: v.secretariat,
                            display: `${v.prefix} - ${v.plate} - ${v.name}`
                        }));
                        this.showVehicleDropdown = true;
                    } catch (error) {
                        console.error('Erro ao buscar veículos:', error);
                    } finally {
                        this.vehicleLoading = false;
                    }
                },

                selectVehicle(vehicle) {
                    this.selectedVehicle = vehicle;
                    this.selectedVehicleId = vehicle.id;
                    this.vehicleSearch = vehicle.display;
                    this.showVehicleDropdown = false;
                }
            }">
            @csrf

            <!-- Busca de Veículo -->
            <div>
                <x-input-label for="vehicle_search" value="Buscar Veículo (Placa ou Prefixo) *" />
                <div class="relative mt-1">
                    <input
                        type="text"
                        id="vehicle_search"
                        x-model="vehicleSearch"
                        @input.debounce.300ms="searchVehicle()"
                        @focus="showVehicleDropdown = true"
                        placeholder="Digite a placa ou prefixo..."
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                        autocomplete="off"
                        required
                    />
                    <input type="hidden" name="vehicle_id" x-model="selectedVehicleId" required>

                    <!-- Dropdown de resultados -->
                    <div x-show="showVehicleDropdown && vehicleResults.length > 0"
                         x-cloak
                         @click.away="showVehicleDropdown = false"
                         class="absolute z-50 mt-1 w-full bg-white dark:bg-navy-800 border border-gray-300 dark:border-navy-600 rounded-md shadow-lg max-h-60 overflow-auto">
                        <template x-for="vehicle in vehicleResults" :key="vehicle.id">
                            <div @click="selectVehicle(vehicle)"
                                 class="px-4 py-2 hover:bg-primary-50 dark:hover:bg-navy-700 cursor-pointer border-b border-gray-200 dark:border-navy-700 last:border-0">
                                <div class="font-medium text-gray-900 dark:text-white" x-text="vehicle.display"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400" x-text="'Secretaria: ' + vehicle.secretariat_name"></div>
                            </div>
                        </template>
                    </div>

                    <!-- Loading -->
                    <div x-show="vehicleLoading" x-cloak class="absolute right-3 top-3">
                        <svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
            </div>

            <!-- Informações do Veículo Selecionado -->
            <div x-show="selectedVehicle" x-cloak class="p-4 bg-blue-50 dark:bg-navy-900/50 border border-blue-200 dark:border-navy-600 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Veículo Selecionado</h3>
                <div class="grid md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Placa:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-2" x-text="selectedVehicle?.plate"></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Prefixo:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-2" x-text="selectedVehicle?.prefix || '-'"></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Nome:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-2" x-text="selectedVehicle?.name"></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Secretaria Atual:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-2" x-text="selectedVehicle?.secretariat_name"></span>
                    </div>
                </div>
            </div>

            <!-- Tipo de Transferência -->
            <div>
                <x-input-label for="type" value="Tipo de Transferência *" />
                <x-ui.select name="type" id="type" class="mt-1" x-model="transferType" required>
                    <option value="">Selecione...</option>
                    <option value="permanent">Permanente</option>
                    <option value="temporary">Empréstimo Temporário</option>
                </x-ui.select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <!-- Secretaria de Destino -->
            <div>
                <x-input-label for="destination_secretariat_id" value="Secretaria de Destino *" />
                <x-ui.select name="destination_secretariat_id" id="destination_secretariat_id" class="mt-1" required>
                    <option value="">Selecione...</option>
                    @foreach($secretariats as $secretariat)
                        <option value="{{ $secretariat->id }}" @selected(old('destination_secretariat_id') == $secretariat->id)>
                            {{ $secretariat->name }}
                        </option>
                    @endforeach
                </x-ui.select>
                <x-input-error :messages="$errors->get('destination_secretariat_id')" class="mt-2" />
            </div>

            <!-- Datas (apenas para empréstimo temporário) -->
            <div x-show="transferType === 'temporary'" x-cloak class="grid md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="start_date" value="Data/Hora de Início *" />
                    <x-text-input
                        type="datetime-local"
                        name="start_date"
                        id="start_date"
                        class="mt-1 block w-full"
                        :value="old('start_date')"
                        x-bind:required="transferType === 'temporary'" />
                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="end_date" value="Data/Hora de Término *" />
                    <x-text-input
                        type="datetime-local"
                        name="end_date"
                        id="end_date"
                        class="mt-1 block w-full"
                        :value="old('end_date')"
                        x-bind:required="transferType === 'temporary'" />
                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                </div>
            </div>

            <!-- Observações -->
            <div>
                <x-input-label for="request_notes" value="Observações" />
                <textarea
                    name="request_notes"
                    id="request_notes"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                    placeholder="Motivo da transferência ou informações adicionais...">{{ old('request_notes') }}</textarea>
                <x-input-error :messages="$errors->get('request_notes')" class="mt-2" />
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                <x-ui.secondary-button :href="route('vehicle-transfers.index')">
                    Cancelar
                </x-ui.secondary-button>
                <x-ui.primary-button type="submit">
                    Solicitar Transferência
                </x-ui.primary-button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
