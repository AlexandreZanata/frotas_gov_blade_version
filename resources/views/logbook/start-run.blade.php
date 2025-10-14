<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Iniciar Corrida" subtitle="Informe os dados da viagem" hide-title-mobile icon="clipboard" />
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
    </div>

    <x-ui.card title="Iniciar Viagem" subtitle="Preencha os dados para iniciar a corrida">
        <form action="{{ route('logbook.store-start-run', $run) }}" method="POST" class="space-y-6" x-data="{
            destinations: [{ value: '' }],
            addDestination() {
                this.destinations.push({ value: '' });
            },
            removeDestination(index) {
                if (this.destinations.length > 1) {
                    this.destinations.splice(index, 1);
                }
            }
        }">
            @csrf

            <!-- Last KM Info -->
            @if($lastKm > 0)
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
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

            <!-- Destinations -->
            <div>
                <x-input-label value="Destinos *" />
                <p class="text-sm text-gray-500 dark:text-navy-400 mb-4">
                    Adicione todos os destinos da viagem em ordem
                </p>

                <div class="space-y-3" x-data>
                    <template x-for="(destination, index) in destinations" :key="index">
                        <div class="flex gap-3 items-start">
                            <div class="flex-1">
                                <input
                                    type="text"
                                    x-model="destination.value"
                                    :name="'destinations[]'"
                                    class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="Ex: Secretaria de Saúde, Bairro Centro"
                                    required
                                >
                            </div>
                            <button
                                type="button"
                                @click="removeDestination(index)"
                                x-show="destinations.length > 1"
                                class="mt-1 p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <button
                    type="button"
                    @click="addDestination()"
                    class="mt-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Adicionar Outro Destino
                </button>

                <x-input-error :messages="$errors->get('destinations')" class="mt-2" />
                <x-input-error :messages="$errors->get('destinations.*')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
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
    </x-ui.card>

    @push('scripts')
        <script>
            // Inicializar com pelo menos um destino preenchido se houver erro de validação
            document.addEventListener('alpine:init', () => {
                const oldDestinations = @json(old('destinations', []));
                if (oldDestinations.length > 0) {
                    Alpine.data('destinationForm', () => ({
                        destinations: oldDestinations.map(dest => ({ value: dest })),
                        addDestination() {
                            this.destinations.push({ value: '' });
                        },
                        removeDestination(index) {
                            if (this.destinations.length > 1) {
                                this.destinations.splice(index, 1);
                            }
                        }
                    }));
                }
            });
        </script>
    @endpush
</x-app-layout>
