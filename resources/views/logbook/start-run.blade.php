<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Iniciar Corrida') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Progress Steps -->
            <x-ui.progress-steps
                :steps="[
                    ['title' => 'Veículo', 'description' => 'Selecione o veículo'],
                    ['title' => 'Checklist', 'description' => 'Verificação do veículo'],
                    ['title' => 'Iniciar', 'description' => 'Dados da corrida'],
                    ['title' => 'Finalizar', 'description' => 'Encerrar corrida'],
                ]"
                :currentStep="3"
            />

            <!-- Flash Messages -->
            <x-ui.flash />

            <!-- Vehicle Info -->
            <x-ui.card>
                <div class="flex items-center gap-4">
                    <x-icon name="car" class="w-10 h-10 text-primary-600 dark:text-primary-400" />
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">
                            {{ $run->vehicle->prefix->name ?? 'N/A' }} - {{ $run->vehicle->name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-navy-300">
                            Placa: {{ $run->vehicle->plate }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Start Run Form -->
            <x-ui.card
                title="Dados da Viagem"
                subtitle="Informe a quilometragem atual e o destino da sua viagem"
            >
                <form action="{{ route('logbook.store-start', $run) }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Last KM Info -->
                        @if($lastKm > 0)
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-sm text-blue-700 dark:text-blue-300">
                                        <p class="font-medium">Última quilometragem registrada</p>
                                        <p class="mt-1">O KM da última corrida deste veículo foi: <strong>{{ number_format($lastKm) }} km</strong></p>
                                        <p class="mt-1 text-xs">O campo abaixo já foi preenchido, mas você pode ajustá-lo se necessário.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Start KM -->
                        <x-ui.km-input
                            name="start_km"
                            label="Quilometragem Atual *"
                            :value="old('start_km', $lastKm)"
                            required
                        />

                        <!-- Origin (optional) -->
                        <div class="space-y-2">
                            <x-input-label for="origin" value="Origem" />
                            <x-text-input
                                type="text"
                                name="origin"
                                id="origin"
                                :value="old('origin', $run->origin ?? 'Pátio da Prefeitura')"
                                class="block w-full"
                                placeholder="Ex: Pátio da Prefeitura"
                            />
                            <x-input-error :messages="$errors->get('origin')" />
                        </div>

                        <!-- Destination -->
                        <div class="space-y-2">
                            <x-input-label for="destination" value="Destino *" />
                            <x-text-input
                                type="text"
                                name="destination"
                                id="destination"
                                :value="old('destination')"
                                class="block w-full"
                                placeholder="Ex: Secretaria de Saúde"
                                required
                            />
                            <x-input-error :messages="$errors->get('destination')" />
                        </div>

                        <!-- Warning about max distance -->
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <p class="font-medium">Atenção</p>
                                    <p class="mt-1">A distância máxima permitida por corrida é de 500 km. Se precisar percorrer mais, finalize esta corrida e inicie uma nova.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('logbook.checklist', $run) }}">
                            <x-secondary-button>
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
            </x-ui.card>
        </div>
    </div>
</x-app-layout>

