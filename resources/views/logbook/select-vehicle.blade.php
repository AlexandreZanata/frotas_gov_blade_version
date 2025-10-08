<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Corrida - Selecionar Veículo" subtitle="Escolha o veículo para iniciar uma corrida" hide-title-mobile icon="car" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Vehicle Selection Form -->
        <x-ui.card title="Selecione o Veículo" subtitle="Pesquise e escolha o veículo que você irá utilizar nesta corrida">
            <form
                action="{{ route('logbook.store-vehicle') }}"
                method="POST"
                x-data="{
                    searchQuery: '',
                    selectedId: null,
                    selectedVehicle: null,
                    results: [],
                    showDropdown: false,
                    loading: false,
                    debounceTimer: null,

                    search() {
                        clearTimeout(this.debounceTimer);

                        if (this.searchQuery.length === 0) {
                            this.results = [];
                            return;
                        }

                        this.loading = true;
                        this.debounceTimer = setTimeout(() => {
                            fetch(`/api/vehicles/search?q=${encodeURIComponent(this.searchQuery)}`)
                                .then(res => res.json())
                                .then(data => {
                                    this.results = data;
                                    this.loading = false;
                                })
                                .catch(err => {
                                    console.error('Erro ao buscar veículos:', err);
                                    this.loading = false;
                                });
                        }, 300);
                    },

                    selectVehicle(vehicle) {
                        if (!vehicle.available) {
                            alert('Este veículo não está disponível no momento.');
                            return;
                        }

                        this.selectedId = vehicle.id;
                        this.selectedVehicle = vehicle;
                        this.searchQuery = `${vehicle.prefix} - ${vehicle.name}`;
                        this.showDropdown = false;
                    },

                    closeDropdown() {
                        setTimeout(() => {
                            this.showDropdown = false;
                        }, 200);
                    }
                }"
            >
                @csrf

                <!-- Smart Search Input -->
                <div class="space-y-4">
                    <div>
                        <x-input-label for="vehicle_search" value="Pesquisar Veículo *" />
                        <div class="relative">
                            <input
                                type="text"
                                id="vehicle_search"
                                x-model="searchQuery"
                                @input="search(); showDropdown = true;"
                                @focus="showDropdown = true"
                                placeholder="Digite o nome, placa ou prefixo do veículo..."
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                                autocomplete="off"
                                required
                            />
                            <input type="hidden" name="vehicle_id" x-model="selectedId" required>

                            <!-- Dropdown de resultados -->
                            <div x-show="showDropdown && (results.length > 0 || searchQuery.length > 0)"
                                 @click.outside="closeDropdown()"
                                 class="absolute z-50 w-full mt-1 bg-white dark:bg-navy-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-80 overflow-auto">

                                <!-- Loading -->
                                <div x-show="loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    Pesquisando veículos...
                                </div>

                                <!-- Resultados -->
                                <template x-for="result in results" :key="result.id">
                                    <div @click="selectVehicle(result)"
                                         @mousedown.prevent
                                         class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-navy-700 border-b border-gray-100 dark:border-navy-700 last:border-b-0">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="result.prefix + ' - ' + result.name"></div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'Placa: ' + result.plate"></div>
                                            </div>
                                            <div class="ml-3">
                                                <span x-show="result.available" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                    Disponível
                                                </span>
                                                <span x-show="!result.available" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                    Em uso
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Sem resultados -->
                                <div x-show="!loading && results.length === 0 && searchQuery.length === 0"
                                     class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    Digite para pesquisar veículos disponíveis
                                </div>

                                <div x-show="!loading && results.length === 0 && searchQuery.length > 0"
                                     class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    Nenhum veículo encontrado
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Digite o nome, placa ou prefixo para pesquisar</p>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
                    </div>

                    <!-- Selected Vehicle Info - SEM x-transition -->
                    <div x-cloak x-show="selectedVehicle" class="mt-6">
                        <div class="rounded-lg border-2 border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20 p-6">
                            <h3 class="text-lg font-medium text-primary-900 dark:text-primary-100 mb-4">Veículo Selecionado</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Placa -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-600 dark:text-navy-400">Placa</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-navy-50 uppercase tracking-wide" x-text="selectedVehicle?.plate"></dd>
                                </div>

                                <!-- Secretaria -->
                                <div>
                                    <dt class="text-sm font-medium text-gray-600 dark:text-navy-400">Secretaria</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-navy-50" x-text="selectedVehicle?.secretariat"></dd>
                                </div>

                                <!-- Nome do Veículo -->
                                <div class="col-span-full">
                                    <dt class="text-sm font-medium text-gray-600 dark:text-navy-400">Nome do Veículo</dt>
                                    <dd class="mt-1 text-base font-medium text-gray-900 dark:text-navy-50" x-text="selectedVehicle?.full_name"></dd>
                                </div>

                                <!-- Status de Disponibilidade -->
                                <div class="col-span-full" x-show="!selectedVehicle?.available">
                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Veículo indisponível</h3>
                                                <p class="mt-1 text-sm text-red-700 dark:text-red-400">Este veículo está em uso no momento.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('logbook.index') }}">
                        <x-secondary-button>
                            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                            Cancelar
                        </x-secondary-button>
                    </a>

                    <x-primary-button
                        type="submit"
                        x-bind:disabled="!selectedId || (selectedVehicle && !selectedVehicle.available)"
                    >
                        Continuar
                        <x-icon name="chevron-right" class="w-4 h-4 ml-2" />
                    </x-primary-button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
