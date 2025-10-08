<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Minhas Corridas" subtitle="Histórico de corridas realizadas" hide-title-mobile icon="car" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('logbook.start-flow') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Nova Corrida</span>
        </a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <x-ui.flash />

            <!-- Active Run Alert -->
            @if($activeRun = app(\App\Services\LogbookService::class)->getUserActiveRun())
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                    <div class="flex items-start">
                        <x-icon name="car" class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0" />
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                Você tem uma corrida em andamento
                            </h3>
                            <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-400">
                                Veículo: {{ $activeRun->vehicle->prefix->name ?? '' }} - {{ $activeRun->vehicle->name }}
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('logbook.start-flow') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-800 dark:text-yellow-200 dark:hover:bg-yellow-700 transition">
                                    Continuar Corrida
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Runs List -->
            <x-ui.card>
                <x-ui.table
                    :headers="['Veículo','Destino','KM Rodados','Data/Hora','Status','Ações']"
                    :searchable="true"
                    search-placeholder="Pesquisar por veículo, destino ou origem..."
                    :search-value="request('search') ?? ''"
                    :pagination="$runs">
                    @forelse($runs as $run)
                        <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <x-icon name="car" class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-navy-50">
                                            {{ $run->vehicle->prefix->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-navy-300">
                                            {{ $run->vehicle->name }}
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-navy-400">
                                            {{ $run->vehicle->plate }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 dark:text-navy-50 font-medium">{{ $run->destination }}</div>
                                @if($run->stop_point)
                                    <div class="text-xs text-gray-500 dark:text-navy-300">
                                        <span class="font-medium">Parada:</span> {{ $run->stop_point }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($run->end_km && $run->start_km)
                                    <div class="text-sm font-semibold text-gray-900 dark:text-navy-50">
                                        {{ number_format($run->end_km - $run->start_km) }} km
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-navy-300">
                                        {{ number_format($run->start_km) }} → {{ number_format($run->end_km) }}
                                    </div>
                                @elseif($run->start_km)
                                    <div class="text-sm text-gray-900 dark:text-navy-50">
                                        Início: {{ number_format($run->start_km) }} km
                                    </div>
                                    <div class="text-xs text-yellow-600 dark:text-yellow-400">
                                        Em andamento
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400 dark:text-navy-400">
                                        Não iniciada
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($run->started_at)
                                    <div class="text-sm text-gray-900 dark:text-navy-50">
                                        {{ $run->started_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-navy-300">
                                        {{ $run->started_at->format('H:i') }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400 dark:text-navy-400">
                                        Não iniciada
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-ui.status-badge :status="$run->status" />
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-ui.action-icon :href="route('logbook.show', $run)" icon="eye" title="Ver Detalhes" variant="primary" />

                                    @if($run->status === 'in_progress')
                                        <x-ui.action-icon :href="route('logbook.start-flow')" icon="play" title="Continuar" variant="success" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <x-icon name="car" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                <h3 class="text-sm font-medium text-gray-900 dark:text-navy-50 mb-1">Nenhuma corrida realizada</h3>
                                <p class="text-sm text-gray-500 dark:text-navy-300 mb-4">Comece sua primeira corrida agora!</p>
                                <a href="{{ route('logbook.start-flow') }}">
                                    <x-primary-button>
                                        <x-icon name="plus" class="w-4 h-4 mr-2" />
                                        Nova Corrida
                                    </x-primary-button>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </x-ui.table>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
