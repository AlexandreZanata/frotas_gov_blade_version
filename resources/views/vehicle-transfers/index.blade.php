<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Transferências de Veículos" subtitle="Gerencie transferências e empréstimos" hide-title-mobile icon="swap" />
    </x-slot>
    <x-slot name="pageActions">
        <div class="flex gap-2">
            <x-ui.action-icon :href="route('vehicle-transfers.create')" icon="plus" title="Nova Transferência" variant="primary" />

            @if(auth()->user()->role->name === 'general_manager' || auth()->user()->role->name === 'sector_manager')
            <x-ui.action-icon :href="route('vehicle-transfers.pending')" icon="clock" title="Pendentes" variant="warning" />
            @endif

            <x-ui.action-icon :href="route('vehicle-transfers.active')" icon="swap" title="Para Devolver" variant="info" />
        </div>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Veículo','Origem','Destino','Tipo','Status','Data','Ações']"
            :searchable="false"
            :pagination="$transfers">
            @forelse($transfers as $transfer)
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
                            {{ Str::limit($transfer->originSecretariat->name, 30) }}
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-300">
                        <div class="max-w-xs truncate" title="{{ $transfer->destinationSecretariat->name }}">
                            {{ Str::limit($transfer->destinationSecretariat->name, 30) }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transfer->isTemporary() ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                            {{ $transfer->getTypeLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transfer->getStatusBadgeClass() }}">
                            {{ $transfer->getStatusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $transfer->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <x-ui.action-icon :href="route('vehicle-transfers.show', $transfer)" icon="eye" title="Ver" variant="primary" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <x-icon name="swap" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" />
                        <p class="mb-4">Nenhuma transferência encontrada.</p>
                        <a href="{{ route('vehicle-transfers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                            <x-icon name="plus" class="w-4 h-4" />
                            <span>Criar Primeira Transferência</span>
                        </a>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
