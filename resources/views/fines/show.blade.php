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

    <x-ui.card title="Detalhes da Multa">
        <!-- Status e Informações Principais -->
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Status</div>
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $fine->status === 'pending_acknowledgement' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400' : '' }}
                        {{ $fine->status === 'pending_payment' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-400' : '' }}
                        {{ $fine->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400' : '' }}
                        {{ $fine->status === 'appealed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-400' : '' }}
                        {{ $fine->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400' : '' }}">
                        {{ $fine->status_label }}
                    </span>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Valor Total</div>
                    <div class="font-bold text-red-600">R$ {{ number_format($fine->amount, 2, ',', '.') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total de Pontos</div>
                    <div class="font-bold text-orange-600">{{ $fine->infractions->sum('points') }} pontos</div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Infrações</div>
                    <div class="font-bold text-blue-600">{{ $fine->infractions->count() }}</div>
                </div>
            </div>
        </div>

        <!-- Auto de Infração -->
        @if($fine->infractionNotice)
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="clipboard" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    Auto de Infração
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label value="Número do Auto" />
                        <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $fine->infractionNotice->notice_number }}
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Código de Segurança" />
                        <div class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100">
                            {{ $fine->infractionNotice->security_code }}
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Autoridade Emissora" />
                        <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $fine->infractionNotice->issuing_authority ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Dados da Multa -->
        <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                <x-icon name="car" class="w-5 h-5 text-green-600 dark:text-green-400" />
                Dados da Multa
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label value="Veículo" />
                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        <div class="font-semibold">{{ $fine->vehicle->plate }}</div>
                        <div class="text-gray-600 dark:text-gray-400">{{ $fine->vehicle->name }}</div>
                    </div>
                </div>
                <div>
                    <x-input-label value="Condutor" />
                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $fine->driver->name }}
                    </div>
                </div>
                <div>
                    <x-input-label value="Data da Infração" />
                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $fine->issued_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div>
                    <x-input-label value="Data de Vencimento" />
                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $fine->due_date?->format('d/m/Y') ?? 'N/A' }}
                    </div>
                </div>
                <div class="md:col-span-2">
                    <x-input-label value="Local da Infração" />
                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $fine->location ?? 'N/A' }}
                    </div>
                </div>
                <div>
                    <x-input-label value="Cadastrado por" />
                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $fine->registeredBy->name }}
                    </div>
                </div>
                <div>
                    <x-input-label value="Cadastrado em" />
                    <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $fine->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Infrações -->
        <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                <x-icon name="list" class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                Infrações Registradas
            </h3>

            <div class="space-y-4">
                @foreach($fine->infractions as $infraction)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
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
                                {{ $infraction->severity === 'leve' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400' : '' }}
                                {{ $infraction->severity === 'media' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-400' : '' }}
                                {{ $infraction->severity === 'grave' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-400' : '' }}
                                {{ $infraction->severity === 'gravissima' ? 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-400' : '' }}">
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
                    </div>
                @endforeach
            </div>

            <div class="mt-4 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                <div class="text-lg font-bold text-primary-900 dark:text-primary-100">
                    Valor Total da Multa: R$ {{ number_format($fine->amount, 2, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Anexos -->
        @if($fine->attachments->count() > 0)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="upload" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    Anexos
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($fine->attachments as $attachment)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $attachment->file_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $attachment->formatted_size }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ $attachment->full_url }}" target="_blank"
                                   class="flex-1 text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition">
                                    Visualizar
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Histórico -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Visualizações -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="eye" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    Histórico de Visualizações
                </h3>
                @if($fine->viewLogs->count() > 0)
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($fine->viewLogs->sortByDesc('viewed_at') as $log)
                            <div class="flex items-start gap-3 text-sm p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
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
                    <p class="text-sm text-gray-500 dark:text-gray-400 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">Nenhuma visualização registrada.</p>
                @endif
            </div>

            <!-- Alterações -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="history" class="w-5 h-5 text-green-600 dark:text-green-400" />
                    Histórico de Alterações
                </h3>
                @if($fine->processes->count() > 0)
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @foreach($fine->processes->sortByDesc('created_at') as $process)
                            <div class="flex items-start gap-3 text-sm p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
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
                    <p class="text-sm text-gray-500 dark:text-gray-400 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">Nenhuma alteração registrada.</p>
                @endif
            </div>
        </div>

        <!-- Atualizar Status -->
        @can('update', $fine)
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="refresh" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    Atualizar Status
                </h3>
                <form action="{{ route('fines.update-status', $fine) }}" method="POST" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="status" value="Novo Status" />
                            <x-ui.select name="status" id="status" class="mt-1" required>
                                <option value="pending_acknowledgement" {{ $fine->status === 'pending_acknowledgement' ? 'selected' : '' }}>Aguardando Ciência</option>
                                <option value="pending_payment" {{ $fine->status === 'pending_payment' ? 'selected' : '' }}>Aguardando Pagamento</option>
                                <option value="paid" {{ $fine->status === 'paid' ? 'selected' : '' }}>Pago</option>
                                <option value="appealed" {{ $fine->status === 'appealed' ? 'selected' : '' }}>Recorrida</option>
                                <option value="cancelled" {{ $fine->status === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </x-ui.select>
                        </div>
                        <div>
                            <x-input-label for="notes" value="Observações" />
                            <x-text-input
                                id="notes"
                                name="notes"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Motivo da alteração (opcional)"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <x-primary-button type="submit" icon="refresh" compact>
                            Atualizar Status
                        </x-primary-button>
                    </div>
                </form>
            </div>
        @endcan
    </x-ui.card>
</x-app-layout>
