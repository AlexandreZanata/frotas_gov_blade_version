<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Registrar Abastecimento" subtitle="Preencha os dados do abastecimento" hide-title-mobile icon="fuel" />
    </x-slot>
    <x-slot name="pageActions">
        {{-- Link para voltar para a tela de finalizar corrida --}}
        <x-ui.action-icon :href="route('logbook.finish', $run)" icon="arrow-left" title="Voltar para Finalizar Corrida" variant="neutral" />
    </x-slot>

    <div class="mb-6 bg-white dark:bg-navy-800 rounded-lg shadow p-6">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-full">
                <x-icon name="car" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">
                    {{ $run->vehicle->prefix->name ?? 'N/A' }} - {{ $run->vehicle->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-navy-300">
                    Placa: {{ $run->vehicle->plate }}
                </p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">KM Atual da Corrida</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ number_format($run->start_km, 0, ',', '.') }} km</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">Destino Principal</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ $run->destinations->first()->destination ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <x-ui.card title="Dados do Abastecimento" subtitle="Preencha as informações do abastecimento realizado">
        <form action="{{ route('logbook.store-fueling', $run) }}" method="POST" enctype="multipart/form-data"
              class="space-y-6"
              x-data="fuelingForm()"
        >
            @csrf

            <div class="space-y-6">
                <div>
                    <x-input-label value="Tipo de Abastecimento *" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="is_manual" value="0" x-model="isManual" class="sr-only peer">
                            <div class="p-4 border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 border-gray-300 dark:border-navy-600 transition">
                                <p class="font-medium text-gray-900 dark:text-navy-50">Posto Credenciado</p>
                                <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">Valor calculado automaticamente</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="is_manual" value="1" x-model="isManual" class="sr-only peer">
                            <div class="p-4 border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 border-gray-300 dark:border-navy-600 transition">
                                <p class="font-medium text-gray-900 dark:text-navy-50">Abastecimento Manual</p>
                                <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">Preenchimento manual dos valores</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Campos do Modo Credenciado --}}
                <template x-if="!isManual">
                    <div>
                        <x-input-label for="gas_station_id" value="Posto de Combustível *" />
                        <x-ui.select name="gas_station_id" id="gas_station_id" class="mt-2"
                                     x-ref="gasStationSelect"
                                     @change="updatePriceFromSelect($event)"
                                     required
                        >
                            <option value="">Selecione o posto...</option>
                            @foreach($gasStations as $station)
                                @php
                                    // Buscar preços para todos os tipos de combustível deste posto
                                    $prices = [];
                                    foreach($fuelTypes as $fuelType) {
                                        $price = \App\Models\fuel\FuelPrice::where('gas_station_id', $station->id)
                                            ->where('fuel_type_id', $fuelType->id)
                                            ->orderBy('effective_date', 'desc')
                                            ->first();
                                        $prices[$fuelType->id] = $price ? $price->price : 0;
                                    }
                                @endphp
                                <option value="{{ $station->id }}"
                                        @selected(old('gas_station_id') == $station->id)
                                        @foreach($prices as $fuelTypeId => $price)
                                            data-price-{{ $fuelTypeId }}="{{ $price }}"
                                    @endforeach
                                >
                                    {{ $station->name }}
                                    @foreach($prices as $fuelTypeId => $price)
                                        @if($price > 0)
                                            - {{ \App\Models\fuel\FuelType::find($fuelTypeId)->name }}: R$ {{ number_format($price, 2, ',', '.') }}/L
                                        @endif
                                    @endforeach
                                </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('gas_station_id')" class="mt-2" />
                    </div>
                </template>

                {{-- Campo de Tipo de Combustível --}}
                <div>
                    <x-input-label for="fuel_type_id" value="Tipo de Combustível *" />

                    {{-- Modo Credenciado: Apenas visualização --}}
                    <template x-if="!isManual">
                        <div>
                            <div class="mt-2 p-3 bg-gray-50 dark:bg-navy-700 rounded-md border border-gray-300 dark:border-navy-600">
                                <p class="text-gray-900 dark:text-navy-50 font-medium">
                                    {{ $vehicleFuelType->name }}
                                </p>
                                <input type="hidden" name="fuel_type_id" value="{{ $vehicleFuelTypeId }}">
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                                Combustível definido pelo veículo
                            </p>
                        </div>
                    </template>

                    {{-- Modo Manual: Select editável --}}
                    <template x-if="isManual">
                        <div>
                            <x-ui.select name="fuel_type_id" id="fuel_type_id" class="mt-2" required>
                                <option value="">Selecione o combustível...</option>
                                @foreach($fuelTypes as $type)
                                    <option value="{{ $type->id }}"
                                        @selected(old('fuel_type_id', $vehicleFuelTypeId) == $type->id)>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>
                    </template>

                    <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="km" value="KM no momento do abastecimento *" />
                    <div class="mt-2">
                        <input
                            type="number"
                            name="km"
                            id="km"
                            value="{{ old('km') ?? $run->start_km }}"
                            min="{{ $run->start_km }}"
                            step="1"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Informe a quilometragem atual"
                        >
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                        KM deve ser maior ou igual ao inicial da corrida ({{ number_format($run->start_km, 0, ',', '.') }} km)
                    </p>
                    <x-input-error :messages="$errors->get('km')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="liters" value="Quantidade de Litros *" />
                    <div class="mt-2 relative">
                        <input
                            type="number"
                            name="liters"
                            id="liters"
                            x-model.number="liters"
                            step="0.001"
                            min="0.1"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pr-12"
                            placeholder="0.000"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-navy-300 text-sm font-medium">L</span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('liters')" class="mt-2" />
                </div>

                {{-- Campos do Modo Manual --}}
                <template x-if="isManual">
                    <div>
                        <x-input-label for="total_value_manual" value="Valor Total (R$) *" />
                        <div class="mt-2 relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500 dark:text-navy-300">R$</span>
                            </div>
                            <input
                                type="number"
                                name="total_value_manual"
                                id="total_value_manual"
                                x-model.number="totalValueManual"
                                step="0.01"
                                min="0"
                                required
                                class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pl-12"
                                placeholder="0.00"
                            >
                            <input type="hidden" name="value_per_liter" x-bind:value="calculatedValuePerLiter">
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-navy-400" x-show="liters > 0 && totalValueManual > 0">
                            Valor por litro calculado: R$ <span x-text="calculatedValuePerLiter"></span>/L
                        </p>
                        <x-input-error :messages="$errors->get('total_value_manual')" class="mt-2" />
                        <x-input-error :messages="$errors->get('value_per_liter')" class="mt-2" />
                    </div>
                </template>

                <template x-if="isManual">
                    <div>
                        <x-input-label for="gas_station_name" value="Nome do Posto *" />
                        <div class="mt-2">
                            <input
                                type="text"
                                name="gas_station_name"
                                id="gas_station_name"
                                value="{{ old('gas_station_name') }}"
                                required
                                class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                                placeholder="Ex: Posto Shell, Posto Ipiranga..."
                            >
                        </div>
                        <x-input-error :messages="$errors->get('gas_station_name')" class="mt-2" />
                    </div>
                </template>

                {{-- Valor Total do Abastecimento --}}
                <div class="p-4 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-primary-700 dark:text-primary-300">Valor Total do Abastecimento:</span>
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            R$ <span x-text="totalValue"></span>
                        </span>
                    </div>
                    <div x-show="isManual && liters > 0 && totalValueManual > 0" class="mt-2 text-xs text-primary-600 dark:text-primary-400">
                        (Valor por litro: R$ <span x-text="calculatedValuePerLiter"></span>)
                    </div>
                </div>

                <div>
                    <x-input-label for="invoice_path" value="Nota Fiscal (Opcional)" />
                    <div class="mt-2">
                        <input
                            type="file"
                            name="invoice_path"
                            id="invoice_path"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-gray-500 dark:text-navy-300 border border-gray-300 dark:border-navy-600 rounded-md cursor-pointer bg-gray-50 dark:bg-navy-700 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-400"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">
                        Formatos aceitos: PDF, JPG, JPEG ou PNG (tamanho máximo: 5MB)
                    </p>
                    <x-input-error :messages="$errors->get('invoice_path')" class="mt-2" />
                </div>

            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('logbook.finish', $run) }}">
                    <x-secondary-button type="button">
                        <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                        Voltar para Finalizar Corrida
                    </x-secondary-button>
                </a>

                <x-primary-button type="submit">
                    <x-icon name="save" class="w-4 h-4 mr-2" />
                    Registrar Abastecimento
                </x-primary-button>
            </div>
        </form>
    </x-ui.card>

    @push('scripts')
        <script>
            function fuelingForm() {
                return {
                    isManual: {{ old('is_manual', 0) == 1 ? 'true' : 'false' }},
                    liters: {{ old('liters', 0) }},
                    totalValueManual: {{ old('total_value_manual', 0) }},
                    pricePerLiter: {{ old('value_per_liter', 0) }},

                    // Calcula o valor total baseado no modo
                    get totalValue() {
                        if (this.isManual) {
                            return parseFloat(this.totalValueManual) || 0;
                        } else {
                            const L = parseFloat(this.liters) || 0;
                            const P = parseFloat(this.pricePerLiter) || 0;
                            return (L * P).toFixed(2);
                        }
                    },

                    // Calcula o valor por litro no modo manual
                    get calculatedValuePerLiter() {
                        if (this.isManual) {
                            const L = parseFloat(this.liters) || 0;
                            const total = parseFloat(this.totalValueManual) || 0;
                            return L > 0 ? (total / L).toFixed(2) : 0;
                        }
                        return this.pricePerLiter;
                    },

                    updatePriceFromSelect(event) {
                        if (!this.isManual) {
                            const selectedOption = event.target.options[event.target.selectedIndex];
                            const selectedFuelType = '{{ $vehicleFuelTypeId }}';

                            if (selectedOption.value && selectedFuelType) {
                                const price = selectedOption.getAttribute('data-price-' + selectedFuelType);
                                this.pricePerLiter = parseFloat(price) || 0;
                            } else {
                                this.pricePerLiter = 0;
                            }
                        }
                    },

                    updateFuelTypePrice() {
                        if (!this.isManual) {
                            const gasStationSelect = document.getElementById('gas_station_id');
                            const selectedFuelType = '{{ $vehicleFuelTypeId }}';

                            if (gasStationSelect && gasStationSelect.value && selectedFuelType) {
                                const selectedOption = gasStationSelect.options[gasStationSelect.selectedIndex];
                                const price = selectedOption.getAttribute('data-price-' + selectedFuelType);
                                this.pricePerLiter = parseFloat(price) || 0;
                            } else {
                                this.pricePerLiter = 0;
                            }
                        }
                    },

                    init() {
                        // Inicializa o preço no carregamento
                        this.$nextTick(() => {
                            this.updateFuelTypePrice();
                        });

                        // Observa mudanças no modo para recalcular
                        this.$watch('isManual', (value) => {
                            if (!value) {
                                // Quando volta para posto credenciado, recalcula o preço
                                this.$nextTick(() => {
                                    this.updateFuelTypePrice();
                                });
                            }
                        });
                    }
                }
            }

            // Selecionar automaticamente o posto se houver apenas um
            document.addEventListener('DOMContentLoaded', function() {
                const gasStationSelect = document.getElementById('gas_station_id');
                if (gasStationSelect && gasStationSelect.options.length === 2) {
                    gasStationSelect.selectedIndex = 1;
                    // Dispara o evento de change para atualizar o preço
                    const event = new Event('change');
                    gasStationSelect.dispatchEvent(event);
                }
            });
        </script>
    @endpush
</x-app-layout>
