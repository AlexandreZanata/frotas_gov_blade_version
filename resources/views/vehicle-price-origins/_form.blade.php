@csrf

<div class="grid gap-4 md:grid-cols-2">
    <!-- Vehicle Selection (only in create) -->
    @if(!isset($vehiclePriceOrigin) || !$vehiclePriceOrigin->id)
        <div class="md:col-span-2" x-data="vehicleSearch()">
            <x-input-label for="vehicle_search" value="Veículo *" />
            <div class="relative">
                <input
                    type="text"
                    id="vehicle_search"
                    x-model="searchQuery"
                    @input="search()"
                    @focus="showDropdown = true"
                    @click.away="closeDropdown()"
                    placeholder="Digite para pesquisar por prefixo, placa ou nome..."
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                    autocomplete="off"
                    required
                />
                <input type="hidden" name="vehicle_id" x-model="selectedId" required>

                <!-- Dropdown de resultados -->
                <div x-show="showDropdown && (results.length > 0 || searchQuery.length > 0)"
                     x-cloak
                     x-transition
                     class="absolute z-50 w-full mt-1 bg-white dark:bg-navy-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto">

                    <!-- Loading -->
                    <div x-show="loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        Pesquisando...
                    </div>

                    <!-- Resultados -->
                    <template x-for="vehicle in results" :key="vehicle.id">
                        <div @click="selectVehicle(vehicle)"
                             class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-navy-700 text-sm text-gray-700 dark:text-gray-300">
                            <div class="font-semibold" x-text="vehicle.prefix_id + ' - ' + vehicle.name"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'Placa: ' + vehicle.plate + ' | Marca: ' + vehicle.brand + ' | Ano: ' + vehicle.model_year"></div>
                        </div>
                    </template>

                    <!-- Sem resultados -->
                    <div x-show="!loading && results.length === 0 && searchQuery.length > 0"
                         class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        Nenhum veículo disponível encontrado.
                    </div>
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Digite para pesquisar veículos por prefixo, placa ou nome</p>
            <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
        </div>

        <script>
            function vehicleSearch() {
                return {
                    searchQuery: '',
                    selectedId: null,
                    results: [],
                    showDropdown: false,
                    loading: false,
                    searchTimeout: null,

                    search() {
                        clearTimeout(this.searchTimeout);

                        if (this.searchQuery.length === 0) {
                            this.results = [];
                            this.selectedId = null;
                            return;
                        }

                        this.loading = true;
                        this.searchTimeout = setTimeout(() => {
                            fetch(`{{ route('api.vehicle-price-origins.available-vehicles') }}?q=${encodeURIComponent(this.searchQuery)}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Erro na resposta da API: ' + response.status);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    this.results = data;
                                    this.loading = false;
                                })
                                .catch(error => {
                                    console.error('Erro ao buscar veículos:', error);
                                    this.results = [];
                                    this.loading = false;
                                    this.showNotification('❌ Erro', 'Erro ao buscar veículos. Tente novamente.', 'error');
                                });
                        }, 300);
                    },

                    selectVehicle(vehicle) {
                        this.searchQuery = vehicle.prefix_id + ' - ' + vehicle.name + ' (' + vehicle.plate + ')';
                        this.selectedId = vehicle.id;
                        this.showDropdown = false;
                    },

                    showNotification(title, message, type = 'success') {
                        const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-700 dark:text-green-400' : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-400';

                        const notification = document.createElement('div');
                        notification.className = `fixed top-4 right-4 z-50 ${bgColor} border px-4 py-3 rounded-lg shadow-lg max-w-md animate-slide-in`;
                        notification.innerHTML = `
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <strong class="font-bold block">${title}</strong>
                                    <span class="block text-sm mt-1">${message}</span>
                                </div>
                                <button onclick="this.parentElement.parentElement.remove()" class="text-current opacity-70 hover:opacity-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        `;

                        document.body.appendChild(notification);
                        setTimeout(() => {
                            notification.style.opacity = '0';
                            notification.style.transform = 'translateX(100%)';
                            setTimeout(() => notification.remove(), 300);
                        }, 4000);
                    },

                    closeDropdown() {
                        setTimeout(() => {
                            this.showDropdown = false;
                        }, 200);
                    }
                }
            }
        </script>
    @else
        <!-- Show vehicle info when editing -->
        <div class="md:col-span-2">
            <x-input-label value="Veículo" />
            <div class="mt-1 p-3 bg-gray-50 dark:bg-navy-800 rounded-md border border-gray-200 dark:border-gray-600">
                <p class="font-semibold text-gray-900 dark:text-white">{{ $vehiclePriceOrigin->vehicle->prefix->name ?? 'N/A' }} - {{ $vehiclePriceOrigin->vehicle->name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Placa: {{ $vehiclePriceOrigin->vehicle->plate }} |
                    Marca: {{ $vehiclePriceOrigin->vehicle->brand->name ?? 'N/A' }} |
                    Ano: {{ $vehiclePriceOrigin->vehicle->model_year }}
                </p>
            </div>
        </div>
    @endif

    <!-- Amount -->
    <div>
        <x-input-label for="amount" value="Valor de Aquisição (R$) *" />
        <x-text-input
            id="amount"
            name="amount"
            type="number"
            step="0.01"
            min="0"
            class="mt-1 block w-full"
            :value="old('amount', $vehiclePriceOrigin->amount ?? '')"
            placeholder="0,00"
            required
        />
        <x-input-error :messages="$errors->get('amount')" class="mt-1" />
    </div>

    <!-- Acquisition Date -->
    <div>
        <x-input-label for="acquisition_date" value="Data de Aquisição *" />
        <x-text-input
            id="acquisition_date"
            name="acquisition_date"
            type="date"
            class="mt-1 block w-full"
            :value="old('acquisition_date', $vehiclePriceOrigin->acquisition_date ?? '')"
            required
        />
        <x-input-error :messages="$errors->get('acquisition_date')" class="mt-1" />
    </div>

    <!-- Acquisition Type -->
    <div class="md:col-span-2">
        <x-input-label for="acquisition_type_id" value="Tipo de Aquisição *" />
        <x-ui.select name="acquisition_type_id" id="acquisition_type_id" class="mt-1" required>
            <option value="">Selecione o tipo de aquisição...</option>
            @foreach($acquisitionTypes as $acquisitionType)
                <option value="{{ $acquisitionType->id }}" @selected(old('acquisition_type_id', $vehiclePriceOrigin->acquisition_type_id ?? '') == $acquisitionType->id)>
                    {{ $acquisitionType->name }}
                </option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('acquisition_type_id')" class="mt-1" />
    </div>
</div>

<div class="flex items-center gap-3 pt-6">
    <x-primary-button icon="save" compact>Salvar</x-primary-button>
    <a href="{{ route('vehicle-price-origins.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
</div>
