<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Veículos para Devolver" subtitle="Empréstimos temporários ativos" hide-title-mobile icon="swap" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-transfers.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Veículo','Origem','Destino','Solicitante','Período','Ações']"
            :searchable="false"
            :pagination="$activeTransfers">
            @forelse($activeTransfers as $transfer)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <x-icon name="car" class="w-5 h-5 text-gray-400 shrink-0" />
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 dark:text-white truncate">
                                    @if($transfer->vehicle->prefix)
                                        {{ $transfer->vehicle->prefix->abbreviation }} -
                                    @endif
                                    {{ $transfer->vehicle->plate }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 truncate">{{ $transfer->vehicle->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        <div class="max-w-xs truncate" title="{{ $transfer->originSecretariat->name }}">
                            {{ Str::limit($transfer->originSecretariat->name, 25) }}
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        <div class="max-w-xs truncate" title="{{ $transfer->destinationSecretariat->name }}">
                            {{ Str::limit($transfer->destinationSecretariat->name, 25) }}
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm">
                        <div class="text-gray-900 dark:text-white font-medium">{{ $transfer->requester->name }}</div>
                    </td>
                    <td class="px-4 py-2 text-sm">
                        <div class="text-gray-900 dark:text-white">
                            {{ $transfer->start_date->format('d/m/Y H:i') }}
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            até {{ $transfer->end_date->format('d/m/Y H:i') }}
                        </div>
                        @if($transfer->end_date->isPast())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 mt-1">
                                Vencido
                            </span>
                        @elseif($transfer->end_date->diffInDays(now()) <= 2)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 mt-1">
                                Vence em breve
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('vehicle-transfers.show', $transfer)" icon="eye" title="Ver" variant="primary" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="swap" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
                        <p>Nenhum empréstimo temporário ativo para devolução.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
