<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Finalizar Corrida" subtitle="Preencha os dados para concluir a viagem" hide-title-mobile icon="clipboard" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <!-- Vehicle Info Card -->
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

        <!-- Trip Info -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">Destinos</p>
                <ol class="text-base font-medium text-gray-900 dark:text-navy-50 list-decimal list-inside">
                    @foreach($run->destinations as $destination)
                        <li>{{ $destination->destination }}</li>
                    @endforeach
                </ol>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">KM Inicial</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ number_format($run->start_km, 0, ',', '.') }} km</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">Iniciada em</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ $run->start_datetime ? $run->start_datetime->format('d/m/Y H:i') : 'Não iniciada' }}</p>
            </div>
        </div>
    </div>

    <x-ui.card title="Finalizar Viagem" subtitle="Preencha os dados para finalizar a corrida">
        <form action="{{ route('logbook.store-finish', $run) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{
            startKm: {{ $run->start_km }},
            endKm: {{ old('end_km', $run->start_km) }},
            // ADIÇÃO AQUI: Inicializa o KM do abastecimento com o KM final
            fuelingKm: {{ old('fueling_km', old('end_km', $run->start_km)) }},
            showFueling: {{ old('add_fueling') ? 'true' : 'false' }},
            fuelingType: '{{ old('fueling_type', 'credenciado') }}',
            get distance() {
                return this.endKm > this.startKm ? this.endKm - this.startKm : 0;
            }
        }" x-init="$watch('endKm', value => fuelingKm = value)"
            @csrf

            <!-- End KM -->
            <div>
                <x-input-label for="end_km" value="Quilometragem Final (KM) *" />
                <div class="mt-2">
                    <input
                        type="number"
                        name="end_km"
                        id="end_km"
                        x-model.number="endKm"
                        value="{{ old('end_km', $run->start_km) }}"
                        min="{{ $run->start_km }}"
                        step="1"
                        required
                        class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                    >
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                    O KM final deve ser maior ou igual ao KM inicial ({{ number_format($run->start_km, 0, ',', '.') }} km)
                </p>
                <x-input-error :messages="$errors->get('end_km')" class="mt-2" />
            </div>

            <!-- Distance Display -->
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Distância percorrida:</span>
                    <span class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                        <span x-text="distance.toLocaleString('pt-BR')"></span> km
                    </span>
                </div>
            </div>

            <!-- Stop Point -->
            <div>
                <x-input-label for="stop_point" value="Ponto de Parada (Opcional)" />
                <div class="mt-2">
                    <input
                        type="text"
                        name="stop_point"
                        id="stop_point"
                        value="{{ old('stop_point') }}"
                        class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Ex: Pátio da Prefeitura, Garagem Central"
                    >
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                    Onde o veículo será estacionado após a corrida
                </p>
                <x-input-error :messages="$errors->get('stop_point')" class="mt-2" />
            </div>

            <!-- Fueling Option -->
            <div class="border-t border-gray-200 dark:border-navy-700 pt-6">
                <div class="flex items-center mb-4">
                    <input
                        type="checkbox"
                        name="add_fueling"
                        id="add_fueling"
                        x-model="showFueling"
                        @checked(old('add_fueling'))
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-navy-700 dark:border-navy-600"
                    >
                    <label for="add_fueling" class="ml-3 cursor-pointer">
                        <span class="block text-sm font-medium text-gray-900 dark:text-navy-50">
                            Desejo registrar um abastecimento
                        </span>
                    </label>
                </div>

                <!-- Fueling Form (shown when checkbox is checked) -->
                <div x-show="showFueling" x-transition class="space-y-6 mt-6 p-6 bg-gray-50 dark:bg-navy-900/50 rounded-lg border border-gray-200 dark:border-navy-700">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-navy-50">Dados do Abastecimento</h4>

                    <!-- Fueling Type Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="fueling_type"
                                value="credenciado"
                                x-model="fuelingType"
                                class="sr-only peer"
                                @checked(old('fueling_type', 'credenciado') === 'credenciado')
                            >
                            <div class="p-4 border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 border-gray-300 dark:border-navy-600 transition">
                                <p class="font-medium text-gray-900 dark:text-navy-50">Posto Credenciado</p>
                                <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">Valor calculado automaticamente</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="fueling_type"
                                value="manual"
                                x-model="fuelingType"
                                class="sr-only peer"
                                @checked(old('fueling_type') === 'manual')
                            >
                            <div class="p-4 border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 border-gray-300 dark:border-navy-600 transition">
                                <p class="font-medium text-gray-900 dark:text-navy-50">Abastecimento Manual</p>
                                <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">Preenchimento manual</p>
                            </div>
                        </label>
                    </div>

                    <!-- Common Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Fueling KM -->
                        <div>
                            <x-input-label for="fueling_km" value="KM de Abastecimento *" />
                            <input
                                type="number"
                                name="fueling_km"
                                id="fueling_km"
                                {{-- ADIÇÃO AQUI: Usa x-model para vincular o campo --}}
                                x-model.number="fuelingKm"
                                min="{{ $run->start_km }}"
                                step="1"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                            >
                            <x-input-error :messages="$errors->get('fueling_km')" class="mt-2" />
                        </div>

                        <!-- Liters -->
                        <div>
                            <x-input-label for="liters" value="Litros Abastecidos *" />
                            <input
                                type="number"
                                name="liters"
                                id="liters"
                                value="{{ old('liters') }}"
                                step="0.01"
                                min="0"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                            >
                            <x-input-error :messages="$errors->get('liters')" class="mt-2" />
                        </div>

                        <!-- Fuel Type -->
                        <div>
                            <x-input-label for="fuel_type_id" value="Tipo de Combustível *" />
                            <x-ui.select name="fuel_type_id" id="fuel_type_id" class="mt-2">
                                <option value="">Selecione...</option>
                                @foreach($fuelTypes as $fuelType)
                                    <option value="{{ $fuelType->id }}" @selected(old('fuel_type_id') == $fuelType->id)>
                                        {{ $fuelType->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-2" />
                        </div>

                        <!-- Gas Station (Credenciado) -->
                        <div x-show="fuelingType === 'credenciado'">
                            <x-input-label for="gas_station_id" value="Posto de Gasolina *" />
                            <x-ui.select name="gas_station_id" id="gas_station_id" class="mt-2">
                                <option value="">Selecione...</option>
                                @foreach($gasStations as $station)
                                    <option value="{{ $station->id }}" @selected(old('gas_station_id') == $station->id)>
                                        {{ $station->name }} - R$ {{ number_format($station->price_per_liter, 2, ',', '.') }}/L
                                    </option>
                                @endforeach
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('gas_station_id')" class="mt-2" />
                        </div>

                        <!-- Gas Station Name (Manual) -->
                        <div x-show="fuelingType === 'manual'" x-cloak>
                            <x-input-label for="gas_station_name" value="Nome do Posto *" />
                            <input
                                type="text"
                                name="gas_station_name"
                                id="gas_station_name"
                                value="{{ old('gas_station_name') }}"
                                class="mt-2 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                                placeholder="Ex: Posto Shell"
                            >
                            <x-input-error :messages="$errors->get('gas_station_name')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Value (Manual only) -->
                    <div x-show="fuelingType === 'manual'" x-cloak>
                        <x-input-label for="total_value" value="Valor Total do Abastecimento (R$) *" />
                        <input
                            type="number"
                            name="total_value"
                            id="total_value"
                            value="{{ old('total_value') }}"
                            step="0.01"
                            min="0"
                            class="mt-2 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="0,00"
                        >
                        <x-input-error :messages="$errors->get('total_value')" class="mt-2" />
                    </div>

                    <!-- Invoice Upload -->
                    <div>
                        <x-input-label for="invoice" value="Nota Fiscal (Opcional)" />
                        <input
                            type="file"
                            name="invoice"
                            id="invoice"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="mt-2 block w-full text-sm text-gray-500 dark:text-navy-300
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 file:text-primary-700
                                hover:file:bg-primary-100
                                dark:file:bg-primary-900/30 dark:file:text-primary-400"
                        >
                        <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">PDF, JPG, JPEG ou PNG. Máximo 5MB.</p>
                        <x-input-error :messages="$errors->get('invoice')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('logbook.start-run', $run) }}">
                    <x-secondary-button type="button">
                        <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                        Voltar
                    </x-secondary-button>
                </a>

                <x-primary-button type="submit">
                    <x-icon name="check" class="w-4 h-4 mr-2" />
                    Finalizar Corrida
                </x-primary-button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
