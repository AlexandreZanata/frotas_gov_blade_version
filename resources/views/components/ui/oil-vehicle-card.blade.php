@props([
    'vehicle',
    'lastOilChange' => null,
    'status' => 'sem_registro',
    'kmProgress' => 0,
    'dateProgress' => 0,
    'currentKm' => 0
])

@php
    $statusConfig = [
        'em_dia' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-800 dark:text-green-300', 'label' => 'Em Dia', 'icon' => 'check-circle'],
        'atencao' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30', 'text' => 'text-yellow-800 dark:text-yellow-300', 'label' => 'Atenção', 'icon' => 'alert-circle'],
        'critico' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-800 dark:text-orange-300', 'label' => 'Crítico', 'icon' => 'alert-triangle'],
        'vencido' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-800 dark:text-red-300', 'label' => 'Vencido', 'icon' => 'x-circle'],
        'sem_registro' => ['bg' => 'bg-gray-100 dark:bg-navy-700', 'text' => 'text-gray-800 dark:text-gray-300', 'label' => 'Sem Registro', 'icon' => 'info'],
    ];
    $config = $statusConfig[$status] ?? $statusConfig['sem_registro'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-navy-800 rounded-lg shadow border border-gray-200 dark:border-navy-700 p-4 hover:shadow-lg transition-shadow duration-200']) }}>
    <!-- Cabeçalho do Card -->
    <div class="flex justify-between items-start mb-3">
        <div class="flex-1 min-w-0">
            <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $vehicle->name }}</h3>
            <p class="text-sm text-gray-600 dark:text-navy-300 uppercase tracking-wide">{{ $vehicle->plate }}</p>
            @if($vehicle->category)
                <p class="text-xs text-gray-500 dark:text-navy-400 mt-0.5">{{ $vehicle->category->name }}</p>
            @endif
        </div>
        <span class="px-2 py-1 text-xs font-semibold rounded {{ $config['bg'] }} {{ $config['text'] }} whitespace-nowrap ml-2">
            {{ $config['label'] }}
        </span>
    </div>

    @if($status !== 'sem_registro' && $lastOilChange)
        <!-- Informações da Última Troca -->
        <div class="text-sm text-gray-600 dark:text-navy-300 space-y-1 mb-3 pb-3 border-b border-gray-200 dark:border-navy-700">
            <div class="flex justify-between">
                <span class="text-xs font-medium">Última troca:</span>
                <span class="text-xs">{{ $lastOilChange->change_date->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs font-medium">KM na troca:</span>
                <span class="text-xs">{{ number_format($lastOilChange->km_at_change, 0, ',', '.') }} km</span>
            </div>
            @if($lastOilChange->service_provider)
                <div class="flex justify-between">
                    <span class="text-xs font-medium">Prestador:</span>
                    <span class="text-xs truncate ml-2">{{ $lastOilChange->service_provider }}</span>
                </div>
            @endif
        </div>

        <!-- Barras de Progresso -->
        <div class="space-y-3 mb-4">
            <!-- Progresso KM -->
            <x-ui.progress-bar
                label="Quilometragem"
                :value="$currentKm"
                :max="$lastOilChange->next_change_km"
                size="md"
            />

            <!-- Progresso Tempo -->
            <x-ui.progress-bar
                label="Tempo (dias)"
                :value="$lastOilChange->change_date->diffInDays(now())"
                :max="$lastOilChange->change_date->diffInDays($lastOilChange->next_change_date)"
                size="md"
            />
        </div>
    @else
        <div class="text-sm text-gray-500 dark:text-navy-400 text-center py-6 border-b border-gray-200 dark:border-navy-700 mb-4">
            <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="font-medium">Nenhuma troca registrada</p>
        </div>
    @endif

    <!-- Ações -->
    <div class="flex gap-2">
        <button
            @click="$dispatch('open-register-modal', { vehicleId: '{{ $vehicle->id }}' })"
            class="flex-1 px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-md font-medium transition-colors duration-200 flex items-center justify-center gap-1.5">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Registrar</span>
        </button>
        <a
            href="{{ route('oil-changes.history', $vehicle->id) }}"
            class="flex-1 px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white text-sm rounded-md font-medium transition-colors duration-200 text-center flex items-center justify-center gap-1.5">
            <x-icon name="clock" class="w-4 h-4" />
            <span>Histórico</span>
        </a>
    </div>
</div>
