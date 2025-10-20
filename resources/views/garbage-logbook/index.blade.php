<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Minhas Coletas" subtitle="Diário de Coleta de Resíduos" hide-title-mobile icon="trash" />
    </x-slot>

    <x-slot name="pageActions">
        @if(isset($unsignedRuns) && $unsignedRuns->isNotEmpty())
            <form action="{{ route('garbage-logbook.runs.sign.all') }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja assinar todas as {{ $unsignedRuns->count() }} coletas pendentes?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium shadow transition"
                        title="Assina todas as suas coletas com status 'Concluída' que ainda não foram assinadas.">
                    <x-icon name="pencil-square" class="w-4 h-4" />
                    <span>Assinar Pendentes ({{ $unsignedRuns->count() }})</span>
                </button>
            </form>
        @endif
        <a href="{{ route('garbage-logbook.start-flow') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Nova Coleta</span>
        </a>
    </x-slot>

    @if($activeRun = app(\App\Services\GarbageLogbookService::class)->getUserActiveRun())
        <div class="mb-4 rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4 border border-yellow-200 dark:border-yellow-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Você tem uma coleta em andamento
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <p>Veículo: <span class="font-semibold">{{ $activeRun->vehicle->vehicle->prefix->name ?? '' }} - {{ $activeRun->vehicle->vehicle->name }}</span></p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('garbage-logbook.start-flow') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-100 dark:hover:bg-yellow-700 transition">
                            Continuar Coleta →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-ui.card>
        <x-ui.table
            :headers="['Veículo','Bairros','KM Rodados','Data/Hora','Status','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por veículo, bairro..."
            :search-value="$search ?? ''"
            :pagination="$runs">
            @forelse($runs as $run)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $run->vehicle->vehicle->prefix->name ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $run->vehicle->vehicle->name }} • {{ $run->vehicle->vehicle->plate }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                            {{ $run->destinations->first()->neighborhood->name ?? 'N/A' }}
                            @if($run->destinations->count() > 1)
                                <span class="text-xs text-gray-500">(+{{ $run->destinations->count() - 1 }} outros)</span>
                            @endif
                        </div>
                        @if($run->stop_point)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Parada: {{ $run->stop_point }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($run->end_km && $run->start_km)
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($run->end_km - $run->start_km) }} km
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ number_format($run->start_km) }} → {{ number_format($run->end_km) }}
                            </div>
                        @elseif($run->start_km)
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Início: {{ number_format($run->start_km) }} km
                            </div>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-100">
                            {{ $run->started_at ? $run->started_at->format('d/m/Y') : '-' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $run->started_at ? $run->started_at->format('H:i') : '-' }}
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($run->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Concluída
                            </span>
                        @elseif($run->status === 'in_progress')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Em Andamento
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ ucfirst($run->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <x-ui.action-icon :href="route('garbage-logbook.show', $run)" icon="eye" title="Ver Detalhes" variant="primary" />

                            @if($run->status === 'completed')
                                @if(!$run->signature?->driver_signed_at)
                                    <form action="{{ route('garbage-logbook.runs.sign.driver', ['runId' => $run->id]) }}" method="POST" class="inline" onsubmit="return confirm('Deseja assinar esta coleta?');">
                                        @csrf
                                        <button type="submit"
                                                class="p-1 rounded-md text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-navy-600 transition"
                                                title="Assinar Coleta">
                                            <x-icon name="pencil" class="w-5 h-5" />
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1 text-green-500 cursor-default" title="Assinado pelo motorista em {{ $run->signature->driver_signed_at->format('d/m/Y H:i') }}">
                                        <x-icon name="check-circle" class="w-5 h-5" />
                                    </span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhuma coleta encontrada.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
