<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Postos Ativos" subtitle="Postos disponíveis para abastecimento no momento" />
    </x-slot>

    <x-ui.card>
        {{-- 1. Adicione um div com uma classe única ao redor da tabela --}}
        <div class="gas-stations-current-table">
            <x-ui.table
                {{-- 2. Volte a usar o header como uma string simples --}}
                :headers="['Posto', 'Endereço', 'Ativo Desde', 'Ativo Até']"
                :pagination="$currentGasStations">

                @forelse($currentGasStations as $current)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                        <td class="px-4 py-2 font-medium">{{ $current->gasStation->name }}</td>
                        <td class="px-4 py-2">{{ $current->gasStation->address ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $current->start_date->format('d/m/Y H:i') }}</td>

                        {{-- 3. A célula da última coluna continua com o alinhamento à direita --}}
                        <td class="px-4 py-2 text-right">{{ $current->end_date ? $current->end_date->format('d/m/Y H:i') : 'Indeterminado' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum posto ativo no momento.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </div>
    </x-ui.card>
</x-app-layout>

{{-- 4. Adicione este bloco de estilo no final do arquivo --}}
@push('styles')
    <style>
        .gas-stations-current-table th:last-child {
            text-align: right;
        }
    </style>
@endpush
