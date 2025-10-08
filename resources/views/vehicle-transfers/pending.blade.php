<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Transferências Pendentes" subtitle="Aprovar ou rejeitar solicitações" hide-title-mobile icon="clock" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-transfers.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Veículo','Solicitante','Origem','Destino','Tipo','Data','Ações']"
            :searchable="false">
            @forelse($pendingTransfers as $transfer)
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
                    <td class="px-4 py-2 text-sm">
                        <div class="text-gray-900 dark:text-white font-medium">{{ $transfer->requester->name }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ $transfer->requester->email }}</div>
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
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transfer->isTemporary() ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                            {{ $transfer->getTypeLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $transfer->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('vehicle-transfers.show', $transfer)" icon="eye" title="Ver" variant="primary" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="clock" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
                        <p>Nenhuma transferência pendente de aprovação.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>

