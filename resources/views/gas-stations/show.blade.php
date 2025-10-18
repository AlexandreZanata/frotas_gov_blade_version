<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ $gasStation->name }}" subtitle="Detalhes do posto" hide-title-mobile icon="fuel"/>
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('gas-stations.edit', $gasStation) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Editar</span>
        </a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações Principais -->
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Informações do Posto">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Nome</p>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $gasStation->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Status</p>
                            <div class="mt-1">
                                @if($gasStation->status === 'active')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        Ativo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Endereço</p>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $gasStation->address ?? '—' }}</p>
                    </div>

                    <div>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            {{ $gasStation->cnpj ? \App\Http\Controllers\fuel\GasStationController::formatCnpj($gasStation->cnpj) : '—' }}
                        </p>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $gasStation->cnpj ?? '—' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-navy-700">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Cadastrado em</p>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $gasStation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Última atualização</p>
                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $gasStation->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Histórico de Cotações -->
            @if(isset($recentQuotations) && $recentQuotations->count() > 0)
                <x-ui.card title="Histórico de Cotações">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                            <thead class="bg-gray-50 dark:bg-navy-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">
                                    Data
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">
                                    Combustível
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">
                                    Preço
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">
                                    Cotação
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                            @foreach($recentQuotations as $price)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $price->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        {{ $price->fuelType->name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                        R$ {{ number_format($price->price, 3, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <a href="{{ route('fuel-quotations.show', $price->quotation) }}"
                                           class="text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                            {{ $price->quotation->name }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            @endif
        </div>

        <!-- Ações Rápidas -->
        <div class="lg:col-span-1">
            <x-ui.card title="Ações">
                <div class="space-y-3">
                    <a href="{{ route('fuel-quotations.create') }}"
                       class="flex items-center justify-between px-4 py-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition group">
                        <span class="text-sm font-medium text-primary-700 dark:text-primary-400">Nova Cotação</span>
                        <svg
                            class="w-5 h-5 text-primary-600 dark:text-primary-400 group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <a href="{{ route('gas-stations.edit', $gasStation) }}"
                       class="flex items-center justify-between px-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition group">
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-400">Editar Dados</span>
                        <svg
                            class="w-5 h-5 text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <form action="{{ route('gas-stations.destroy', $gasStation) }}"
                          method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir este posto? Esta ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full flex items-center justify-between px-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition group">
                            <span class="text-sm font-medium text-red-700 dark:text-red-400">Excluir Posto</span>
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>

