<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ $fuelQuotation->name }}" subtitle="Detalhes da cotação" hide-title-mobile icon="trending-up" />
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Conteúdo Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informações da Cotação -->
            <x-ui.card title="Informações da Cotação">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Data da Cotação</p>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $fuelQuotation->quotation_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Método de Cálculo</p>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            {{ $fuelQuotation->calculation_method === 'simple_average' ? 'Média Aritmética Simples' : 'Método Personalizado' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Status</p>
                        <div class="mt-1">
                            @if($fuelQuotation->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Concluída
                                </span>
                            @elseif($fuelQuotation->status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    Rascunho
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    Cancelada
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Criado por</p>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $fuelQuotation->user->name }}</p>
                    </div>
                </div>

                @if($fuelQuotation->notes)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Observações</p>
                    <p class="mt-1 text-sm text-gray-700 dark:text-navy-200">{{ $fuelQuotation->notes }}</p>
                </div>
                @endif
            </x-ui.card>

            <!-- Preços Coletados -->
            <x-ui.card title="Preços Coletados">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                        <thead class="bg-gray-50 dark:bg-navy-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Posto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Combustível</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Preço (R$/L)</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Comprovante</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                            @foreach($fuelQuotation->prices as $price)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $price->gasStation->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-navy-200">{{ $price->fuelType->name }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">R$ {{ number_format($price->price, 3, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($price->evidence_path)
                                        <a href="{{ $price->evidence_url }}" target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            <!-- Médias e Descontos -->
            <x-ui.card title="Médias Calculadas e Descontos Aplicados">
                <div class="space-y-4">
                    @foreach($fuelQuotation->discounts as $discount)
                    <div class="p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                        <div class="grid grid-cols-5 gap-4 items-center">
                            <div>
                                <p class="text-xs text-gray-600 dark:text-navy-300">Combustível</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $discount->fuelType->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-navy-300">Preço Médio</p>
                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">R$ {{ number_format($discount->average_price, 3, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-navy-300">Desconto</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($discount->discount_percentage, 2, ',', '.') }}%</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-navy-300">Valor do Desconto</p>
                                <p class="text-sm font-medium text-red-600 dark:text-red-400">- R$ {{ number_format($discount->average_price * ($discount->discount_percentage / 100), 3, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-navy-300">Preço Final</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">R$ {{ number_format($discount->final_price, 3, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>

            <!-- Tabela Comparativa -->
            @if(count($comparison) > 0)
            <x-ui.card title="Tabela Comparativa Final">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                        <thead class="bg-gray-50 dark:bg-navy-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Combustível</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Preço Final</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Preços de Bomba</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Resultado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                            @foreach($comparison as $comp)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $comp['fuel_type'] }}</td>
                                <td class="px-4 py-3 text-sm font-bold text-green-600 dark:text-green-400">
                                    R$ {{ number_format($comp['final_price'], 3, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @forelse($comp['pump_prices'] as $pump)
                                        <div class="mb-1">
                                            <span class="text-gray-700 dark:text-navy-200">{{ $pump->gasStation->name }}:</span>
                                            <span class="font-medium">R$ {{ number_format($pump->pump_price, 3, ',', '.') }}</span>
                                            @if($pump->evidence_path)
                                                <a href="{{ $pump->evidence_url }}" target="_blank" class="ml-1 text-primary-600">
                                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-gray-400">—</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @foreach($comp['pump_prices'] as $pump)
                                        @php
                                            $isFavorable = $pump->pump_price > $comp['final_price'];
                                            $difference = (($pump->pump_price - $comp['final_price']) / $comp['final_price']) * 100;
                                        @endphp
                                        <div class="mb-1">
                                            <span class="font-semibold {{ $isFavorable ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $isFavorable ? '✓ Favorável' : '✗ Desfavorável' }}
                                                ({{ number_format(abs($difference), 2, ',', '.') }}%)
                                            </span>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
            @endif
        </div>

        <!-- Sidebar de Ações -->
        <div class="lg:col-span-1">
            <x-ui.card title="Ações">
                <div class="space-y-3">
                    <a href="{{ route('fuel-quotations.index') }}"
                       class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-navy-700 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-600 transition group">
                        <span class="text-sm font-medium text-gray-700 dark:text-navy-200">Voltar à Lista</span>
                        <svg class="w-5 h-5 text-gray-600 dark:text-navy-300 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <a href="{{ route('fuel-quotations.create') }}"
                       class="flex items-center justify-between px-4 py-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition group">
                        <span class="text-sm font-medium text-primary-700 dark:text-primary-400">Nova Cotação</span>
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>

                    <button onclick="window.print()"
                            class="w-full flex items-center justify-between px-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition group">
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-400">Imprimir Relatório</span>
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </button>

                    <form action="{{ route('fuel-quotations.destroy', $fuelQuotation) }}"
                          method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir esta cotação? Esta ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full flex items-center justify-between px-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition group">
                            <span class="text-sm font-medium text-red-700 dark:text-red-400">Excluir Cotação</span>
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </x-ui.card>

            <!-- Informações Adicionais -->
            <x-ui.card title="Informações" class="mt-6">
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-navy-300">Criado em</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $fuelQuotation->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-navy-300">Última atualização</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $fuelQuotation->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-navy-300">Total de preços</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $fuelQuotation->prices->count() }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>

