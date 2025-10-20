<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Finalizar Coleta" subtitle="Preencha os dados para concluir a coleta"
                          hide-title-mobile icon="clipboard"/>
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('garbage-logbook.index')" icon="arrow-left" title="Voltar" variant="neutral"/>

        <!-- Botão para abastecimento independente -->
        <a href="{{ route('garbage-logbook.fueling', $run) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium shadow transition">
            <x-icon name="fuel" class="w-4 h-4"/>
            <span>Abastecer</span>
        </a>
    </x-slot>

    <!-- Vehicle Info Card -->
    <div class="mb-6 bg-white dark:bg-navy-800 rounded-lg shadow p-6">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-full">
                <x-icon name="truck" class="w-8 h-8 text-primary-600 dark:text-primary-400"/>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">
                    {{ $run->vehicle->vehicle->prefix->name ?? 'N/A' }} - {{ $run->vehicle->vehicle->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-navy-300">
                    Placa: {{ $run->vehicle->vehicle->plate }}
                </p>
            </div>
        </div>

        <!-- Trip Info -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">Bairros</p>
                <ol class="text-base font-medium text-gray-900 dark:text-navy-50 list-decimal list-inside">
                    @foreach($run->destinations as $destination)
                        <li>{{ $destination->neighborhood->name }}</li>
                    @endforeach
                </ol>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">KM Inicial</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ number_format($run->start_km, 0, ',', '.') }}
                    km</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">Iniciada em</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ $run->started_at ? $run->started_at->format('d/m/Y H:i') : 'Não iniciada' }}</p>
            </div>
        </div>
    </div>

    <x-ui.card title="Finalizar Coleta" subtitle="Preencha os dados para finalizar a coleta">
        <form action="{{ route('garbage-logbook.store-finish', $run) }}" method="POST" class="space-y-6" x-data="{
            startKm: {{ $run->start_km }},
            endKm: {{ old('end_km', $run->start_km) }},
            get distance() {
                return this.endKm > this.startKm ? this.endKm - this.startKm : 0;
            }
        }">
            @csrf

            <!-- End KM -->
            <div>
                <x-input-label for="end_km" value="Quilometragem Final (KM) *"/>
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
                    O KM final deve ser maior ou igual ao KM inicial ({{ number_format($run->start_km, 0, ',', '.') }}
                    km)
                </p>
                <x-input-error :messages="$errors->get('end_km')" class="mt-2"/>
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

            <!-- Pesagem -->
            <div>
                <x-input-label for="pesagem" value="Pesagem (Kg) - Opcional"/>
                <div class="mt-2">
                    <input
                        type="number"
                        name="pesagem"
                        id="pesagem"
                        value="{{ old('pesagem') }}"
                        step="0.01"
                        min="0"
                        class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Ex: 1500.50"
                    >
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                    Peso total coletado em quilogramas
                </p>
                <x-input-error :messages="$errors->get('pesagem')" class="mt-2"/>
            </div>

            <!-- Stop Point -->
            <div>
                <x-input-label for="stop_point" value="Ponto de Parada (Opcional)"/>
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
                    Onde o veículo será estacionado após a coleta
                </p>
                <x-input-error :messages="$errors->get('stop_point')" class="mt-2"/>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                <div class="flex space-x-3">
                    <a href="{{ route('garbage-logbook.start-run', $run) }}">
                        <x-secondary-button type="button">
                            <x-icon name="arrow-left" class="w-4 h-4 mr-2"/>
                            Voltar
                        </x-secondary-button>
                    </a>

                    <a href="{{ route('garbage-logbook.fueling', $run) }}">
                        <x-secondary-button type="button" variant="outline">
                            <x-icon name="fuel" class="w-4 h-4 mr-2"/>
                            Abastecer Agora
                        </x-secondary-button>
                    </a>
                </div>

                <x-primary-button type="submit" x-bind:disabled="!endKm || endKm < startKm">
                    <x-icon name="check" class="w-4 h-4 mr-2"/>
                    Finalizar Coleta
                </x-primary-button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
