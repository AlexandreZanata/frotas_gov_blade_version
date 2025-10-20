<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Relatórios de Coleta"
            subtitle="Histórico de coletas de lixo realizadas"
            hide-title-mobile
            icon="document-chart-bar"
        />
    </x-slot>

    <x-ui.card>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">Lista de Coletas</h3>
                <p class="text-sm text-gray-500 dark:text-navy-300">Visualize o histórico de coletas de lixo</p>
            </div>
        </div>

        <x-ui.table
            :headers="['Veículo', 'Motorista', 'Bairros', 'Data', 'Status']"
            :searchable="true"
            search-placeholder="Pesquisar coletas..."
        >
            @forelse($runs as $run)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="text-sm font-medium text-gray-900 dark:text-navy-50">
                            {{ $run->garbageVehicle->vehicle->prefix->name ?? 'N/A' }} - {{ $run->garbageVehicle->vehicle->name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-navy-300">
                            {{ $run->garbageVehicle->vehicle->plate }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            {{ $run->garbageUser->user->name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-navy-300">
                            {{ $run->garbageUser->user->email }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            @foreach($run->destinations as $destination)
                                <span class="inline-block bg-gray-100 dark:bg-navy-700 text-gray-800 dark:text-navy-200 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                                    {{ $destination->neighborhood->name }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            {{ $run->created_at->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm">
                            @if($run->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Concluída
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    Em andamento
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhuma coleta encontrada.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $runs->links() }}
        </div>
    </x-ui.card>
</x-app-layout>
