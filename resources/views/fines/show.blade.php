<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes da Multa" subtitle="Visualizar informações completas da multa" hide-title-mobile icon="clipboard" />
    </x-slot>
    <x-slot name="pageActions">
        <div class="flex gap-2">
            <a href="{{ route('fines.pdf', $fine) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                </svg>
                <span>Gerar PDF</span>
            </a>
            <a href="{{ route('fines.edit', $fine) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow transition">
                <x-icon name="edit" class="w-4 h-4" />
                <span>Editar</span>
            </a>
            <a href="{{ route('fines.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium shadow transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
                <span>Voltar</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Card de Status e Informações Principais -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-ui.stat-card
                    title="Status"
                    :value="$fine->status_label"
                    :color="$fine->status_color"
                    icon="clipboard" />

                <x-ui.stat-card
                    title="Valor Total"
                    value="R$ {{ number_format($fine->total_amount, 2, ',', '.') }}"
                    color="red"
                    icon="cash" />

                <x-ui.stat-card
                    title="Total de Pontos"
                    :value="$fine->total_points . ' pontos'"
                    color="orange"
                    icon="alert" />

                <x-ui.stat-card
                    title="Infrações"
                    :value="$fine->infractions->count()"
                    color="blue"
                    icon="list" />
            </div>

            <!-- Informações do Auto de Infração -->
            @if($fine->infractionNotice)
            <x-ui.card>
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-icon name="document" class="w-5 h-5 inline" /> Auto de Infração
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $fine->infractionNotice->notice_number }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Código de Segurança</dt>
                            <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100">
                                {{ $fine->infractionNotice->security_code }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Autoridade Emissora</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $fine->infractionNotice->issuing_authority ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </x-ui.card>
            @endif

            <!-- Informações da Multa -->
            <x-ui.card>
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-icon name="info" class="w-5 h-5 inline" /> Informações da Multa
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Veículo</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <div class="font-semibold">{{ $fine->vehicle->plate }}</div>
                                <div class="text-gray-600 dark:text-gray-400">{{ $fine->vehicle->name }}</div>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Condutor</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $fine->driver->name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data da Infração</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $fine->issued_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data de Vencimento</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $fine->due_date?->format('d/m/Y') ?? 'N/A' }}
                            </dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Local</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $fine->location ?? 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cadastrado por</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $fine->registeredBy->name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cadastrado em</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $fine->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </x-ui.card>

            <!-- Infrações -->
            <x-ui.card>
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-icon name="list" class="w-5 h-5 inline" /> Infrações Registradas
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($fine->infractions as $infraction)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $infraction->infraction_code }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $infraction->description }}
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $infraction->severity === 'leve' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $infraction->severity === 'media' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $infraction->severity === 'grave' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $infraction->severity === 'gravissima' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($infraction->severity) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Valor Base:</span>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        R$ {{ number_format($infraction->base_amount, 2, ',', '.') }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Taxas:</span>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        R$ {{ number_format($infraction->extra_fees, 2, ',', '.') }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Desconto:</span>
                                    <div class="font-medium text-green-600">
                                        -R$ {{ number_format($infraction->discount_amount, 2, ',', '.') }}
                                        @if($infraction->discount_percentage > 0)
                                            ({{ number_format($infraction->discount_percentage, 2) }}%)
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Pontos:</span>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $infraction->points }}
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Valor Final:</span>
                                    <div class="font-bold text-red-600">
                                        R$ {{ number_format($infraction->final_amount, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            @if($infraction->attachments->count() > 0)
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Anexos:</div>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($infraction->attachments as $attachment)
                                            <a href="{{ $attachment->full_url }}" target="_blank"
                                               class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs rounded-md transition">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                </svg>
                                                {{ $attachment->file_name }} ({{ $attachment->formatted_size }})
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <!-- Atualizar Status -->
            @can('update', $fine)
            <x-ui.card>
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <x-icon name="refresh" class="w-5 h-5 inline" /> Atualizar Status
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('fines.update-status', $fine) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Novo Status
                                </label>
                                <select name="status" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="pending_acknowledgement" {{ $fine->status === 'pending_acknowledgement' ? 'selected' : '' }}>
                                        Aguardando Ciência
                                    </option>
                                    <option value="pending_payment" {{ $fine->status === 'pending_payment' ? 'selected' : '' }}>
                                        Aguardando Pagamento
                                    </option>
                                    <option value="paid" {{ $fine->status === 'paid' ? 'selected' : '' }}>
                                        Pago
                                    </option>
                                    <option value="appealed" {{ $fine->status === 'appealed' ? 'selected' : '' }}>
                                        Recorrida
                                    </option>
                                    <option value="cancelled" {{ $fine->status === 'cancelled' ? 'selected' : '' }}>
                                        Cancelada
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Observações
                                </label>
                                <input type="text" name="notes"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                       placeholder="Motivo da alteração (opcional)">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                                Atualizar Status
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.card>
            @endcan

            <!-- Histórico de Visualizações e Alterações -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Visualizações -->
                <x-ui.card>
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <x-icon name="eye" class="w-5 h-5 inline" /> Histórico de Visualizações
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($fine->viewLogs->count() > 0)
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                @foreach($fine->viewLogs->sortByDesc('viewed_at') as $log)
                                    <div class="flex items-start gap-3 text-sm">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $log->user->name }}
                                            </div>
                                            <div class="text-gray-600 dark:text-gray-400">
                                                {{ $log->viewed_at->format('d/m/Y H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma visualização registrada.</p>
                        @endif
                    </div>
                </x-ui.card>

                <!-- Histórico de Alterações -->
                <x-ui.card>
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            <x-icon name="history" class="w-5 h-5 inline" /> Histórico de Alterações
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($fine->processes->count() > 0)
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                @foreach($fine->processes->sortByDesc('created_at') as $process)
                                    <div class="flex items-start gap-3 text-sm">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-1.5 flex-shrink-0"></div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $process->user->name }}
                                            </div>
                                            <div class="text-gray-600 dark:text-gray-400">
                                                {{ $process->notes }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-500">
                                                {{ $process->created_at->format('d/m/Y H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nenhuma alteração registrada.</p>
                        @endif
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>

