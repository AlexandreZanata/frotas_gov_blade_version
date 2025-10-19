<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Detalhes do Abastecimento"
            subtitle="Informações completas do registro"
            hide-title-mobile
            icon="document-text"
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

    <div class="grid gap-8 md:grid-cols-2">
        {{-- Informações do Abastecimento --}}
        <x-ui.card class="rounded-2xl">
            <div class="p-6 border-b border-gray-100 dark:border-navy-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <x-icon name="document-text" class="w-5 h-5 text-white" />
                    </div>
                    Informações do Abastecimento
                </h3>
            </div>
            <div class="p-6">
                @php($items = [
                    ['label' => 'Veículo', 'value' => e($fueling->vehicle->name ?? 'N/A') . ' (' . e($fueling->vehicle->plate ?? 'N/A') . ')', 'icon' => 'truck'],
                    ['label' => 'Secretaria', 'value' => e($fueling->vehicle->secretariat->name ?? 'N/A'), 'icon' => 'office-building'],
                    ['label' => 'Posto', 'value' => e($fueling->gasStation->name ?? $fueling->gas_station_name), 'icon' => 'building-store'],
                    ['label' => 'Combustível', 'value' => e($fueling->fuelType->name ?? 'N/A'), 'icon' => 'fuel-pump'],
                    ['label' => 'Data do Abastecimento', 'value' => $fueling->fueled_at->format('d/m/Y H:i'), 'icon' => 'calendar'],
                    ['label' => 'Quilometragem', 'value' => number_format($fueling->km, 0, ',', '.') . ' km', 'icon' => 'speedometer'],
                    ['label' => 'Litros Abastecidos', 'value' => number_format($fueling->liters, 3, ',', '.') . ' L', 'icon' => 'beaker'],
                    ['label' => 'Valor Total', 'value' => 'R$ ' . number_format($fueling->value, 2, ',', '.'), 'icon' => 'currency-dollar'],
                    ['label' => 'Valor por Litro', 'value' => 'R$ ' . number_format($fueling->value_per_liter, 2, ',', '.'), 'icon' => 'tag'],
                    ['label' => 'Código Público', 'value' => e($fueling->public_code ?? 'N/A'), 'icon' => 'qrcode'],
                    ['label' => 'Registrado por', 'value' => e($fueling->user->name ?? 'N/A'), 'icon' => 'user'],
                ])

                <div class="space-y-4">
                    @foreach($items as $item)
                        <div class="flex items-center justify-between py-3 border-b border-gray-50 dark:border-navy-600 last:border-0">
                            <div class="flex items-center space-x-3">
                                <x-icon name="{{ $item['icon'] }}" class="w-4 h-4 text-gray-400" />
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $item['label'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $item['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-ui.card>

        {{-- Logs de Visualização --}}
        <x-ui.card class="rounded-2xl">
            <div class="p-6 border-b border-gray-100 dark:border-navy-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <x-icon name="eye" class="w-5 h-5 text-white" />
                    </div>
                    Logs de Visualização
                </h3>
            </div>
            <div class="p-6">
                <h4 class="text-md font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    Quem visualizou este registro
                </h4>

                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($viewLogs as $log)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-navy-600 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                    <x-icon name="user" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $log->user->name ?? 'Usuário Desconhecido' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $log->viewed_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">IP: {{ $log->ip_address }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $log->user_agent ? Str::limit($log->user_agent, 25) : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <x-icon name="eye-off" class="w-16 h-16 mx-auto mb-4 opacity-50" />
                            <p class="text-lg font-medium">Nenhuma visualização registrada</p>
                            <p class="text-sm">Este registro ainda não foi visualizado por outros usuários.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
