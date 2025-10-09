<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Multas" subtitle="Gestão de multas e infrações" hide-title-mobile icon="clipboard" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('fines.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Nova Multa</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Auto/Veículo','Condutor','Data','Local','Valor Total','Status','Visualizada','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por placa, condutor, auto de infração..."
            :search-value="$search ?? ''"
            :pagination="$fines">
            @forelse($fines as $fine)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $fine->infractionNotice?->notice_number ?? 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 uppercase">
                            {{ $fine->vehicle->plate }}
                        </div>
                    </td>
                    <td class="px-4 py-2">{{ $fine->driver->name }}</td>
                    <td class="px-4 py-2">{{ $fine->issued_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ Str::limit($fine->location ?? '-', 30) }}</td>
                    <td class="px-4 py-2 font-semibold">R$ {{ number_format($fine->total_amount, 2, ',', '.') }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $fine->status_color === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400' : '' }}
                            {{ $fine->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400' : '' }}
                            {{ $fine->status_color === 'orange' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-400' : '' }}
                            {{ $fine->status_color === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400' : '' }}
                            {{ $fine->status_color === 'gray' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                            {{ $fine->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        @if($fine->first_viewed_at)
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Sim</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">Não</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('fines.show', $fine)" icon="eye" title="Ver" variant="primary" />
                            <x-ui.action-icon :href="route('fines.edit', $fine)" icon="edit" title="Editar" variant="info" />
                            <a href="{{ route('fines.pdf', $fine) }}" target="_blank"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                               title="PDF">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                </svg>
                            </a>
                            <x-ui.confirm-form
                                :action="route('fines.destroy', $fine)"
                                method="DELETE"
                                message="Tem certeza que deseja excluir esta multa?"
                                title="Excluir Multa"
                                icon="trash"
                                variant="danger">
                                Excluir
                            </x-ui.confirm-form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhuma multa cadastrada.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>

