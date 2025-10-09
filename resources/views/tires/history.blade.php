<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-history mr-2"></i> Histórico do Pneu
            </h2>
            <x-ui.secondary-button :href="route('tires.stock')">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </x-ui.secondary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Informações do Pneu --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $tire->brand }} {{ $tire->model }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">Série: {{ $tire->serial_number }}</p>
                    </div>
                    @php
                        $conditionColors = [
                            'Novo' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            'Bom' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'Atenção' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                            'Crítico' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                        ];
                        $statusColors = [
                            'Em Estoque' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            'Em Uso' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            'Em Manutenção' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                            'Recapagem' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'Descartado' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                        ];
                    @endphp
                    <div class="flex gap-2">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $conditionColors[$tire->condition] ?? 'bg-gray-100' }}">
                            {{ $tire->condition }}
                        </span>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$tire->status] ?? 'bg-gray-100' }}">
                            {{ $tire->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tipo</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $tire->inventoryItem->name ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Vida Útil</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format(($tire->current_km / $tire->lifespan_km) * 100, 1) }}%
                        </p>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-2">
                            @php
                                $percentage = ($tire->current_km / $tire->lifespan_km) * 100;
                                $barColor = $percentage >= 90 ? 'bg-red-600' : ($percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ number_format($tire->current_km) }} / {{ number_format($tire->lifespan_km) }} km
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Veículo Atual</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $tire->vehicle->name ?? 'Nenhum' }}
                        </p>
                        @if($tire->current_position)
                            <p class="text-xs text-gray-500 dark:text-gray-400">Posição: {{ $tire->current_position }}</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Data de Compra</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($tire->purchase_date)->format('d/m/Y') }}
                        </p>
                        @if($tire->purchase_price)
                            <p class="text-xs text-gray-500 dark:text-gray-400">R$ {{ number_format($tire->purchase_price, 2, ',', '.') }}</p>
                        @endif
                    </div>
                </div>

                @if($tire->notes)
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-1">Observações:</p>
                        <p class="text-sm text-blue-800 dark:text-blue-200">{{ $tire->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Linha do Tempo de Eventos --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">
                    <i class="fas fa-clock mr-2"></i> Histórico de Eventos
                </h3>

                @if($tire->events->count() > 0)
                    <div class="relative">
                        {{-- Linha vertical --}}
                        <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                        <div class="space-y-6">
                            @foreach($tire->events->sortByDesc('event_date') as $event)
                                @php
                                    $eventIcons = [
                                        'Cadastro' => 'fa-plus-circle text-green-500',
                                        'Instalação' => 'fa-wrench text-blue-500',
                                        'Rodízio' => 'fa-sync text-purple-500',
                                        'Troca' => 'fa-exchange-alt text-orange-500',
                                        'Manutenção' => 'fa-tools text-yellow-500',
                                        'Recapagem' => 'fa-recycle text-teal-500',
                                        'Descarte' => 'fa-trash text-red-500',
                                    ];
                                @endphp

                                <div class="relative flex items-start gap-4">
                                    {{-- Ícone --}}
                                    <div class="relative z-10 flex items-center justify-center w-16 h-16 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-full">
                                        <i class="fas {{ $eventIcons[$event->event_type] ?? 'fa-circle text-gray-500' }} text-2xl"></i>
                                    </div>

                                    {{-- Conteúdo --}}
                                    <div class="flex-1 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $event->event_type }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y H:i') }}
                                                </p>
                                            </div>
                                            <div class="text-right text-sm">
                                                <p class="text-gray-600 dark:text-gray-400">Por: {{ $event->user->name }}</p>
                                                @if($event->km_at_event)
                                                    <p class="text-gray-500 dark:text-gray-500">{{ number_format($event->km_at_event) }} km</p>
                                                @endif
                                            </div>
                                        </div>

                                        <p class="text-gray-700 dark:text-gray-300">
                                            {{ $event->description }}
                                        </p>

                                        @if($event->vehicle)
                                            <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    <i class="fas fa-car mr-1"></i>
                                                    Veículo: {{ $event->vehicle->name }} ({{ $event->vehicle->plate }})
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-history text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Nenhum evento registrado ainda</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

