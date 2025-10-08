<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Iniciar Corrida') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <x-ui.flash />

            <!-- Vehicle Info Card -->
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-6">
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
            </div>

            <!-- Start Run Form -->
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">Iniciar Viagem</h3>
                    <p class="text-sm text-gray-500 dark:text-navy-300 mt-1">Preencha os dados para iniciar a corrida</p>
                </div>

                <form action="{{ route('logbook.store-start-run', $run) }}" method="POST" class="p-6">
                    @csrf

                    <!-- Last KM Info -->
                    @if($lastKm > 0)
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    <p class="font-medium">Última quilometragem registrada</p>
                                    <p class="mt-1">O KM da última corrida foi: <strong>{{ number_format($lastKm, 0, ',', '.') }} km</strong></p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Max KM Limit Info -->
                    @if(isset($maxAllowedData) && $maxAllowedData['has_limit'])
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-green-700 dark:text-green-300">
                                    <p class="font-medium">Limite de quilometragem calculado</p>
                                    <p class="mt-1">Média: <strong>{{ number_format($maxAllowedData['average_km'], 2, ',', '.') }} km</strong> | Máximo: <strong>{{ number_format($maxAllowedData['max_km'], 2, ',', '.') }} km</strong></p>
                                </div>
                            </div>
                        </div>
                    @elseif(isset($maxAllowedData) && !$maxAllowedData['has_limit'])
                        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <p class="font-medium">{{ $maxAllowedData['message'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <!-- Start KM -->
                        <div>
                            <x-input-label for="start_km" value="Quilometragem Atual (KM) *" />
                            <div class="mt-2">
                                <input
                                    type="number"
                                    name="start_km"
                                    id="start_km"
                                    value="{{ old('start_km', $lastKm) }}"
                                    min="{{ $lastKm }}"
                                    step="1"
                                    required
                                    class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                                O KM inicial deve ser maior ou igual ao último KM registrado ({{ number_format($lastKm, 0, ',', '.') }} km)
                            </p>
                            <x-input-error :messages="$errors->get('start_km')" class="mt-2" />
                        </div>

                        <!-- Destination -->
                        <div>
                            <x-input-label for="destination" value="Destino *" />
                            <div class="mt-2">
                                <input
                                    type="text"
                                    name="destination"
                                    id="destination"
                                    value="{{ old('destination') }}"
                                    required
                                    class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="Ex: Secretaria de Saúde, Bairro Centro"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('destination')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('logbook.checklist', $run) }}">
                            <x-secondary-button type="button">
                                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                                Voltar
                            </x-secondary-button>
                        </a>

                        <x-primary-button type="submit">
                            Iniciar Viagem
                            <x-icon name="chevron-right" class="w-4 h-4 ml-2" />
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
