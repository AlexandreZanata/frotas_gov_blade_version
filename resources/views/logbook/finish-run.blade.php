<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Finalizar Corrida') }}
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
                :currentStep="4"
            />

            <!-- Flash Messages -->
            <x-ui.flash />

            <!-- Active Run Info -->
            <x-ui.card title="Corrida em Andamento">
                <div class="space-y-4">
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200 dark:border-navy-700">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-navy-300">Origem</p>
                            <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ $run->origin }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-navy-300">Destino</p>
                            <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ $run->destination }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-navy-300">KM Inicial</p>
                            <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ number_format($run->start_km) }} km</p>
                        </div>
                    </div>

                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-sm text-green-700 dark:text-green-300">
                            <strong>Iniciada em:</strong> {{ $run->started_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Finish Run Form -->
            <x-ui.card
                title="Finalizar Viagem"
                subtitle="Informe a quilometragem final e, opcionalmente, registre um abastecimento"
            >
                <form action="{{ route('logbook.store-finish', $run) }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- End KM -->
                        <x-ui.km-input
                            name="end_km"
                            label="Quilometragem Final *"
                            :value="old('end_km', $run->start_km)"
                            required
                        />

                        <!-- Distance Calculator -->
                        <div
                            x-data="{
                                startKm: {{ $run->start_km }},
                                endKm: {{ old('end_km', $run->start_km) }},
                                get distance() {
                                    return this.endKm > this.startKm ? this.endKm - this.startKm : 0;
                                }
                            }"
                            class="p-4 bg-gray-50 dark:bg-navy-900 rounded-lg"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-navy-300">Distância percorrida:</span>
                                <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                    <span x-text="distance.toLocaleString('pt-BR')"></span> km
                                </span>
                            </div>
                        </div>

                        <!-- Stop Point (optional) -->
                        <div class="space-y-2">
                            <x-input-label for="stop_point" value="Ponto de Parada (Opcional)" />
                            <x-text-input
                                type="text"
                                name="stop_point"
                                id="stop_point"
                                :value="old('stop_point')"
                                class="block w-full"
                                placeholder="Ex: Estacionamento da Secretaria"
                            />
                            <p class="text-xs text-gray-500 dark:text-navy-400">Informe onde o veículo ficará estacionado</p>
                            <x-input-error :messages="$errors->get('stop_point')" />
                        </div>

                        <!-- Fueling Option -->
                        <div class="p-4 border-2 border-dashed border-gray-300 dark:border-navy-600 rounded-lg">
                            <div class="flex items-start">
                                <input
                                    type="checkbox"
                                    name="add_fueling"
                                    id="add_fueling"
                                    class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-navy-700 dark:border-navy-600"
                                >
                                <label for="add_fueling" class="ml-3 cursor-pointer">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-navy-50">
                                        Desejo registrar um abastecimento
                                    </span>
                                    <span class="block text-xs text-gray-500 dark:text-navy-300 mt-1">
                                        Marque esta opção se você abasteceu o veículo durante a corrida
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('logbook.index') }}">
                            <x-secondary-button>
                                <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                                Voltar
                            </x-secondary-button>
                        </a>

                        <x-primary-button type="submit">
                            <x-icon name="save" class="w-4 h-4 mr-2" />
                            Finalizar Corrida
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Checklist do Veículo') }}
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
                :currentStep="2"
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

            <!-- Checklist Form -->
            <x-ui.card
                title="Checklist de Verificação"
                subtitle="Verifique todos os itens antes de iniciar a corrida. Itens marcados como 'Problema' serão notificados ao gestor."
            >
                <form action="{{ route('logbook.store-checklist', $run) }}" method="POST">
                    @csrf

                    @if($lastChecklistState)
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    <p class="font-medium">Estado anterior do veículo carregado</p>
                                    <p class="mt-1">Os campos foram pré-preenchidos com a última verificação deste veículo. Você pode alterar conforme necessário.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Checklist Items Grid -->
                    <div class="space-y-4">
                        @forelse($checklistItems as $item)
                            <x-ui.checklist-item
                                :item="$item"
                                :name="'item_' . $item->id"
                                :previousStatus="$lastChecklistState[$item->id]['status'] ?? null"
                                :previousNotes="$lastChecklistState[$item->id]['notes'] ?? null"
                            />
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-navy-300">Nenhum item de checklist configurado.</p>
                                <p class="text-sm text-gray-400 dark:text-navy-400 mt-1">Entre em contato com o administrador.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- General Notes -->
                    <div class="mt-6 space-y-2">
                        <x-input-label for="general_notes" value="Observações Gerais (Opcional)" />
                        <textarea
                            name="general_notes"
                            id="general_notes"
                            rows="4"
                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Adicione observações gerais sobre o estado do veículo..."
                        >{{ old('general_notes') }}</textarea>
                        <x-input-error :messages="$errors->get('general_notes')" />
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('logbook.cancel', $run) }}"
                           onclick="return confirm('Tem certeza que deseja cancelar esta corrida?')"
                        >
                            <x-secondary-button>
                                <x-icon name="close" class="w-4 h-4 mr-2" />
                                Cancelar Corrida
                            </x-secondary-button>
                        </a>

                        <x-primary-button type="submit">
                            Continuar
                            <x-icon name="chevron-right" class="w-4 h-4 ml-2" />
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>

