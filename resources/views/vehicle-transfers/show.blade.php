<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes da Transferência" subtitle="Informações completas da solicitação" hide-title-mobile icon="swap" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-transfers.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <!-- Informações Principais -->
    <x-ui.card title="Informações da Transferência">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $vehicleTransfer->getStatusBadgeClass() }}">
                    {{ $vehicleTransfer->getStatusLabel() }}
                </span>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo</label>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $vehicleTransfer->isTemporary() ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                    {{ $vehicleTransfer->getTypeLabel() }}
                </span>
            </div>

            <!-- Data da Solicitação -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data da Solicitação</label>
                <p class="text-gray-900 dark:text-white">{{ $vehicleTransfer->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Data de Processamento -->
            @if($vehicleTransfer->processed_at)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de Processamento</label>
                <p class="text-gray-900 dark:text-white">{{ $vehicleTransfer->processed_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif

            <!-- Período (para temporários) -->
            @if($vehicleTransfer->isTemporary())
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Período do Empréstimo</label>
                <div class="flex items-center gap-4">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Início:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $vehicleTransfer->start_date->format('d/m/Y H:i') }}</span>
                    </div>
                    <span class="text-gray-400">→</span>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Término:</span>
                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $vehicleTransfer->end_date->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Data de Devolução -->
            @if($vehicleTransfer->returned_at)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de Devolução</label>
                <p class="text-gray-900 dark:text-white">{{ $vehicleTransfer->returned_at->format('d/m/Y H:i') }}</p>
            </div>
            @endif
        </div>
    </x-ui.card>

    <!-- Informações do Veículo -->
    <x-ui.card title="Veículo">
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Placa</label>
                <p class="text-gray-900 dark:text-white font-medium uppercase">{{ $vehicleTransfer->vehicle->plate }}</p>
            </div>

            @if($vehicleTransfer->vehicle->prefix)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prefixo</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $vehicleTransfer->vehicle->prefix->abbreviation }}</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nome</label>
                <p class="text-gray-900 dark:text-white">{{ $vehicleTransfer->vehicle->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Modelo</label>
                <p class="text-gray-900 dark:text-white">{{ $vehicleTransfer->vehicle->brand }} {{ $vehicleTransfer->vehicle->model_year }}</p>
            </div>
        </div>
    </x-ui.card>

    <!-- Secretarias -->
    <x-ui.card title="Transferência">
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Secretaria de Origem</label>
                <p class="text-gray-900 dark:text-white">{{ $vehicleTransfer->originSecretariat->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Secretaria de Destino</label>
                <p class="text-gray-900 dark:text-white">{{ $vehicleTransfer->destinationSecretariat->name }}</p>
            </div>
        </div>
    </x-ui.card>

    <!-- Pessoas Envolvidas -->
    <x-ui.card title="Pessoas Envolvidas">
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Solicitante</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $vehicleTransfer->requester->name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $vehicleTransfer->requester->email }}</p>
            </div>

            @if($vehicleTransfer->approver)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ $vehicleTransfer->isApproved() ? 'Aprovado por' : 'Rejeitado por' }}
                </label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $vehicleTransfer->approver->name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $vehicleTransfer->approver->email }}</p>
            </div>
            @endif
        </div>
    </x-ui.card>

    <!-- Observações -->
    @if($vehicleTransfer->request_notes || $vehicleTransfer->approver_notes)
    <x-ui.card title="Observações">
        @if($vehicleTransfer->request_notes)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações do Solicitante</label>
            <p class="text-gray-900 dark:text-white bg-gray-50 dark:bg-navy-900/50 p-3 rounded-lg">{{ $vehicleTransfer->request_notes }}</p>
        </div>
        @endif

        @if($vehicleTransfer->approver_notes)
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações do Aprovador</label>
            <p class="text-gray-900 dark:text-white bg-gray-50 dark:bg-navy-900/50 p-3 rounded-lg">{{ $vehicleTransfer->approver_notes }}</p>
        </div>
        @endif
    </x-ui.card>
    @endif

    <!-- Ações -->
    @if($vehicleTransfer->isPending() && $vehicleTransfer->canBeApprovedBy(auth()->user()))
    <x-ui.card title="Ações">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Aprovar -->
            <div class="flex-1">
                <form action="{{ route('vehicle-transfers.approve', $vehicleTransfer) }}" method="POST" x-data="{ showNotes: false }">
                    @csrf
                    <div x-show="showNotes" x-cloak class="mb-3">
                        <x-input-label for="approver_notes" value="Observações (opcional)" />
                        <textarea
                            name="approver_notes"
                            id="approver_notes"
                            rows="2"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-green-500 dark:focus:border-green-600 focus:ring-green-500 dark:focus:ring-green-600 rounded-md shadow-sm"
                            placeholder="Observações sobre a aprovação..."></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="showNotes = !showNotes" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
                            <span x-text="showNotes ? 'Esconder' : 'Adicionar observações'"></span>
                        </button>
                        <button type="submit" class="ml-auto inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                            <x-icon name="check" class="w-4 h-4" />
                            Aprovar Transferência
                        </button>
                    </div>
                </form>
            </div>

            <!-- Rejeitar -->
            <div class="flex-1">
                <form action="{{ route('vehicle-transfers.reject', $vehicleTransfer) }}" method="POST" x-data="{ showForm: false }">
                    @csrf
                    <div x-show="showForm" x-cloak class="mb-3">
                        <x-input-label for="reject_notes" value="Motivo da Rejeição *" />
                        <textarea
                            name="approver_notes"
                            id="reject_notes"
                            rows="2"
                            x-bind:required="showForm"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-red-500 dark:focus:border-red-600 focus:ring-red-500 dark:focus:ring-red-600 rounded-md shadow-sm"
                            placeholder="Informe o motivo da rejeição..."></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="showForm = !showForm" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                            <x-icon name="x" class="w-4 h-4" />
                            <span x-text="showForm ? 'Confirmar Rejeição' : 'Rejeitar Transferência'"></span>
                        </button>
                        <button type="submit" x-show="showForm" x-cloak class="inline-flex items-center gap-2 px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded-lg transition">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.card>
    @endif

    <!-- Devolução -->
    @if($vehicleTransfer->isApproved() && $vehicleTransfer->isTemporary() && !$vehicleTransfer->returned_at && $vehicleTransfer->canBeReturnedBy(auth()->user()))
    <x-ui.card title="Devolução do Veículo">
        <form action="{{ route('vehicle-transfers.return', $vehicleTransfer) }}" method="POST" x-data="{ showNotes: false }">
            @csrf
            <div x-show="showNotes" x-cloak class="mb-3">
                <x-input-label for="return_notes" value="Observações sobre a devolução (opcional)" />
                <textarea
                    name="return_notes"
                    id="return_notes"
                    rows="2"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                    placeholder="Condição do veículo, observações, etc..."></textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="showNotes = !showNotes" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
                    <span x-text="showNotes ? 'Esconder' : 'Adicionar observações'"></span>
                </button>
                <button type="submit" class="ml-auto inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <x-icon name="arrow-left" class="w-4 h-4" />
                    Devolver Veículo
                </button>
            </div>
        </form>
    </x-ui.card>
    @endif
</x-app-layout>

