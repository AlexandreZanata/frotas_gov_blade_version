<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes da Corrida') }}
        </h2>
    </x-slot>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Vehicle and Status -->
            <x-ui.card>
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <x-icon name="car" class="w-12 h-12 text-primary-600 dark:text-primary-400" />
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-navy-50">
                                {{ $run->vehicle->prefix->name ?? 'N/A' }} - {{ $run->vehicle->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-navy-300">
                                Placa: {{ $run->vehicle->plate }}
                            </p>
                    </div>
                    <x-ui.status-badge :status="$run->status" class="text-lg px-4 py-2" />
                </div>
            </x-ui.card>

            <!-- Run Details -->
            <x-ui.card title="Informações da Viagem">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-navy-300">Motorista</h4>
                        <p class="mt-1 text-base text-gray-900 dark:text-navy-50">{{ $run->user->name }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-navy-300">CPF</h4>
                        <p class="mt-1 text-base text-gray-900 dark:text-navy-50">{{ $run->user->cpf }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-navy-300">Destinos</h4>
                        <ol class="mt-1 list-decimal list-inside space-y-1">
                            @foreach($run->destinations as $destination)
                                <li class="text-base text-gray-900 dark:text-navy-50">{{ $destination->destination }}</li>
                            @endforeach
                        </ol>
                    </div>
                    @if($run->stop_point)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-navy-300 flex items-center gap-2">
                            <x-icon name="map-pin" class="w-4 h-4" />
                            Ponto de Parada
                        </h4>
                        <p class="mt-1 text-base text-gray-900 dark:text-navy-50">{{ $run->stop_point }}</p>
                    </div>
                    @endif
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-navy-300">Data/Hora Início</h4>
                        <p class="mt-1 text-base text-gray-900 dark:text-navy-50">
                            {{ $run->started_at ? $run->started_at->format('d/m/Y H:i') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-navy-300">Data/Hora Término</h4>
                        <p class="mt-1 text-base text-gray-900 dark:text-navy-50">
                            {{ $run->finished_at ? $run->finished_at->format('d/m/Y H:i') : 'Em andamento' }}
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Kilometrage -->
            <x-ui.card title="Quilometragem">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-navy-300">KM Inicial</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-navy-50">
                            {{ number_format($run->start_km ?? 0) }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-navy-300">KM Final</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-navy-50">
                            {{ number_format($run->end_km ?? 0) }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-navy-300">Percorrido</p>
                        <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ $run->end_km && $run->start_km ? number_format($run->end_km - $run->start_km) : '0' }} km
                        </p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Checklist -->
            @if($run->checklist)
                <x-ui.card title="Checklist Realizado">
                    @if($run->checklist->notes)
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-navy-900 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 dark:text-navy-200">Observações Gerais:</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-navy-300">{{ $run->checklist->notes }}</p>
                        </div>
                    @endif

                    <div class="space-y-3">
                        @foreach($run->checklist->answers as $answer)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-navy-700 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-navy-50">{{ $answer->item->name }}</p>
                                    @if($answer->notes)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-navy-300">{{ $answer->notes }}</p>
                                    @endif
                                </div>
                                <div>
                                    @if($answer->status === 'ok')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            OK
                                        </span>
                                    @elseif($answer->status === 'attention')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            Atenção
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            Problema
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            <!-- Actions -->
            <div class="flex justify-between items-center">
                <a href="{{ route('logbook.index') }}">
                    <x-secondary-button>
                        <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                        Voltar para Minhas Corridas
                    </x-secondary-button>
                </a>

                @if($run->status === 'in_progress')
                    <a href="{{ route('logbook.start-flow') }}">
                        <x-primary-button>
                            <x-icon name="play-circle" class="w-4 h-4 mr-2" />
                            Continuar Corrida
                        </x-primary-button>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
