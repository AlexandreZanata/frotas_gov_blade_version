<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Comunicação de Defeitos" icon="exclamation-triangle" />
    </x-slot>

    <x-ui.card>
        {{-- Aqui você pode adicionar os filtros de busca --}}
        <div class="mb-4">
            <a href="{{ route('defect-reports.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Comunicar Novo Defeito</span>
            </a>
        </div>

        <x-ui.table :headers="['Veículo', 'Placa', 'Usuário', 'Status', 'Data', 'Ações']" :pagination="$defectReports">
            @forelse($defectReports as $report)
                <tr>
                    <td class="px-4 py-2">{{ $report->vehicle->prefix->name ?? '' }} - {{ $report->vehicle->name }}</td>
                    <td class="px-4 py-2">{{ $report->vehicle->plate }}</td>
                    <td class="px-4 py-2">{{ $report->user->name }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @switch($report->status)
                                @case('open') bg-red-100 text-red-800 @break
                                @case('in_progress') bg-yellow-100 text-yellow-800 @break
                                @case('resolved') bg-green-100 text-green-800 @break
                            @endswitch
                        ">{{ ucfirst($report->status) }}</span>
                    </td>
                    <td class="px-4 py-2">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 text-right">
                        <x-ui.action-icon :href="route('defect-reports.show', $report)" icon="eye" title="Ver Detalhes" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center">Nenhuma ficha de defeito encontrada.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
