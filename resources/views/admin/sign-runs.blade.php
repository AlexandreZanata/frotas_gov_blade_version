<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Assinar Corridas" subtitle="Painel do Administrador" />
    </x-slot>

    <x-slot name="pageActions">
        @if($pendingRuns->isNotEmpty())
            <form action="{{ route('admin.runs.sign.all') }}" method="POST" onsubmit="return confirm('Tem certeza que deseja assinar todas as {{ $pendingRuns->total() }} corridas pendentes nesta página e outras?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium shadow transition">
                    <x-icon name="pencil-square" class="w-4 h-4" />
                    <span>Assinar Todas as Pendentes</span>
                </button>
            </form>
        @endif
    </x-slot>

    <x-ui.card>
        {{-- Formulário de Filtro --}}
        <form method="GET" action="{{ route('admin.runs.sign.index') }}" class="mb-4 p-4 bg-gray-50 dark:bg-navy-800 rounded-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="user_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Motorista:</label>
                    <input type="text" name="user_name" id="user_name" value="{{ request('user_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-navy-900 dark:border-navy-600">
                </div>
                <div>
                    <label for="vehicle_plate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Filtrar por Placa:</label>
                    <input type="text" name="vehicle_plate" id="vehicle_plate" value="{{ request('vehicle_plate') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-navy-900 dark:border-navy-600">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring ring-primary-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filtrar
                    </button>
                </div>
            </div>
        </form>

        <x-ui.table
            :headers="['Motorista', 'Secretaria', 'Veículo', 'Data da Corrida', 'Assinatura do Motorista', 'Ações']"
            :pagination="$pendingRuns">
            @forelse($pendingRuns as $run)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">{{ $run->user->name }}</td>
                    <td class="px-4 py-2">{{ $run->user->secretariat->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ $run->vehicle->name }}</div>
                        <div class="text-xs text-gray-500">{{ $run->vehicle->plate }}</div>
                    </td>
                    <td class="px-4 py-2">{{ $run->started_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 text-green-600 dark:text-green-400">
                        <div class="flex items-center gap-1">
                            <x-icon name="check-circle" class="w-5 h-5" />
                            <span>Assinado</span>
                        </div>
                        <div class="text-xs text-gray-500">{{ $run->signature->driver_signed_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="px-4 py-2">
                        <x-ui.action-icon :href="route('logbook.show', $run)" icon="eye" title="Ver Detalhes da Corrida" variant="primary" />
                        {{-- Futuramente, pode ter um botão de assinatura individual aqui --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhuma corrida pendente de assinatura de administrador encontrada.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
