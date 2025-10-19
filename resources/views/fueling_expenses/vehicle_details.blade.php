<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Detalhes de Abastecimentos do Veículo"
            subtitle="Histórico completo de abastecimentos"
            hide-title-mobile
            icon="truck"
        />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon
            :href="route('fueling_expenses.index')"
            icon="arrow-left"
            title="Voltar"
            variant="neutral"
        />
    </x-slot>

    <div x-data="signatureModal()">
        {{-- Card Superior Melhorado --}}
        <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-lg border border-gray-200 dark:border-navy-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8">
                <div class="flex flex-col lg:flex-row items-center justify-between">
                    <div class="flex items-center space-x-6 mb-6 lg:mb-0">
                        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <x-icon name="truck" class="w-10 h-10 text-white" />
                        </div>
                        <div class="text-white">
                            <h1 class="text-3xl font-bold">{{ $vehicle->name ?? 'N/A' }}</h1>
                            <p class="text-blue-100 text-lg mt-1">
                                Placa: <span class="font-mono font-bold">{{ $vehicle->plate ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="text-center">
                            <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Secretaria</p>
                            <p class="text-white text-xl font-bold mt-2">{{ $vehicle->secretariat->name ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                            <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">Total Gasto</p>
                            <p class="text-white text-2xl font-bold mt-2">
                                R$ {{ number_format($vehicleExpense->total_fuel_cost, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Estatísticas Rápidas --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-navy-700 border-t border-gray-200 dark:border-navy-600">
                <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-600 dark:text-gray-300">
                    <div class="flex items-center space-x-2">
                        <x-icon name="calendar" class="w-4 h-4" />
                        <span>{{ $fuelings->total() }} abastecimentos</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <x-icon name="fuel-pump" class="w-4 h-4" />
                        <span>{{ $fuelings->where('signature.admin_signature_id')->count() }} assinados</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <x-icon name="clock" class="w-4 h-4" />
                        <span>{{ $fuelings->where('signature.admin_signature_id', null)->count() }} pendentes</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela de Abastecimentos --}}
        <x-ui.card class="rounded-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Histórico de Abastecimentos</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Total: {{ $fuelings->total() }} registros
                </div>
            </div>

            <x-ui.table
                :headers="['Data', 'Posto', 'Combustível', 'Litros', 'Valor Total', 'Valor/L', 'KM', 'Assinatura', 'Ações']"
                :searchable="true"
                search-placeholder="Pesquisar abastecimento..."
                :pagination="$fuelings">
                @forelse($fuelings as $fueling)
                    <tr class="border-b border-gray-100 dark:border-navy-700 hover:bg-gray-50 dark:hover:bg-navy-700/30 transition-colors
                        {{ !$fueling->signature || !$fueling->signature->admin_signature_id ? 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-l-yellow-400' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($fueling->fueled_at)->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($fueling->fueled_at)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ $fueling->gasStation->name ?? $fueling->gas_station_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $fueling->fuelType->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ number_format($fueling->liters, 3, ',', '.') }} L
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-900 dark:text-white">
                                R$ {{ number_format($fueling->value, 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                R$ {{ number_format($fueling->value_per_liter, 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-mono text-gray-600 dark:text-gray-300">
                                {{ number_format($fueling->km, 0, ',', '.') }} km
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($fueling->signature && $fueling->signature->admin_signature_id)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200 dark:border-green-800">
                                    <x-icon name="check" class="w-3 h-3 mr-1" />
                                    Assinado
                                </span>
                            @else
                                <button type="button"
                                        @click="openSignatureModal('{{ $fueling->id }}')"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105">
                                    <x-icon name="pencil" class="w-3 h-3 mr-1" />
                                    Assinar
                                </button>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <x-ui.action-icon
                                    :href="route('fueling_expenses.fueling_detail', $fueling->id)"
                                    icon="eye"
                                    title="Ver Detalhes"
                                    variant="primary"
                                    class="hover:scale-110 transition-transform"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="text-gray-400 dark:text-gray-500">
                                <x-icon name="fuel-pump" class="w-16 h-16 mx-auto mb-4 opacity-50" />
                                <p class="text-xl font-medium mb-2">Nenhum abastecimento encontrado</p>
                                <p class="text-sm">Não há registros de abastecimento para este veículo.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        {{-- Modal de Confirmação de Assinatura --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-navy-800 rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <x-icon name="exclamation" class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Confirmar Assinatura
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Você deseja assinar esse abastecimento? Esta ação não pode ser desfeita e confirma que você revisou e aprovou este registro de abastecimento.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button"
                                @click="confirmSignature()"
                                :disabled="loading"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-3 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors">
                            <span x-show="!loading">Confirmar Assinatura</span>
                            <span x-show="loading" class="flex items-center">
                                <x-icon name="refresh" class="animate-spin w-4 h-4 mr-2" />
                                Processando...
                            </span>
                        </button>
                        <button type="button"
                                @click="showModal = false"
                                :disabled="loading"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-navy-600 shadow-sm px-4 py-3 bg-white dark:bg-navy-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function signatureModal() {
            return {
                showModal: false,
                fuelingId: null,
                loading: false,

                openSignatureModal(id) {
                    this.fuelingId = id;
                    this.showModal = true;
                },

                async confirmSignature() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/fueling-expenses/sign/${this.fuelingId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showModal = false;
                            location.reload();
                        } else {
                            alert('Erro: ' + result.message);
                        }
                    } catch (error) {
                        alert('Erro ao processar assinatura');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
