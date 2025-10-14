<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Agendamento de Postos" subtitle="Gestão de postos agendados para abastecimento" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('scheduled_gas_stations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Novo Agendamento</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table :headers="['Posto', 'Início', 'Fim', 'Agendado por', 'Status', 'Ações']" :pagination="$scheduledGasStations">
            @forelse($scheduledGasStations as $schedule)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $schedule->gasStation->name }}</td>
                    <td class="px-4 py-2">{{ $schedule->start_date->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2">{{ $schedule->end_date ? $schedule->end_date->format('d/m/Y H:i') : 'Indefinido' }}</td>
                    <td class="px-4 py-2">{{ $schedule->admin->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">
                        <span @class([
                            'px-2 py-1 text-xs font-semibold rounded-full',
                            'bg-yellow-100 text-yellow-800' => !$schedule->is_processed,
                            'bg-green-100 text-green-800' => $schedule->is_processed,
                        ])>
                            {{ $schedule->is_processed ? 'Processado' : 'Pendente' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('scheduled_gas_stations.edit', $schedule)" icon="edit" title="Editar" variant="info" />
                            <x-ui.confirm-form :action="route('scheduled_gas_stations.destroy', $schedule)" method="DELETE" message="Deseja realmente excluir este agendamento?" title="Excluir Agendamento" icon="trash" variant="danger" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum agendamento encontrado.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
