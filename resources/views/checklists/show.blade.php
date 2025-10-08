<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes do Checklist" subtitle="Análise completa do checklist do veículo" hide-title-mobile icon="clipboard-check" />
    </x-slot>

    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('checklists.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <div class="space-y-6">
        <!-- Informações Gerais -->
        <div class="grid gap-6 md:grid-cols-2">
            <x-ui.card title="Informações do Checklist">
                @php($items=[
                    ['label'=>'Veículo','value'=>($checklist->run->vehicle->prefix ? $checklist->run->vehicle->prefix->abbreviation . ' - ' : '') . $checklist->run->vehicle->plate . ' - ' . $checklist->run->vehicle->name,'bold'=>true],
                    ['label'=>'Marca/Modelo','value'=>$checklist->run->vehicle->brand . ' ' . $checklist->run->vehicle->model],
                    ['label'=>'Motorista','value'=>$checklist->user->name,'bold'=>true],
                    ['label'=>'E-mail','value'=>$checklist->user->email],
                    ['label'=>'Secretaria','value'=>$checklist->run->vehicle->secretariat->name ?? '-'],
                    ['label'=>'Data do Checklist','value'=>$checklist->created_at->format('d/m/Y H:i')],
                ])
                <x-ui.detail-list :items="$items" />
            </x-ui.card>

            <x-ui.card title="Status">
                <div class="space-y-4">
                    <!-- Status de Aprovação -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-navy-400">Status de Aprovação</dt>
                        <dd class="mt-1">
                            @if($checklist->approval_status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400">
                                    Pendente
                                </span>
                            @elseif($checklist->approval_status === 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400">
                                    Aprovado
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                                    Rejeitado
                                </span>
                            @endif
                        </dd>
                    </div>

                    <!-- Possui Defeitos -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-navy-400">Possui Defeitos?</dt>
                        <dd class="mt-1">
                            @if($checklist->has_defects)
                                @php($problemCount = $checklist->answers()->where('status', 'problem')->count())
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                                    <x-icon name="alert" class="w-4 h-4" />
                                    Sim - {{ $problemCount }} {{ $problemCount === 1 ? 'Problema' : 'Problemas' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400">
                                    <x-icon name="check" class="w-4 h-4" />
                                    Não
                                </span>
                            @endif
                        </dd>
                    </div>
                </div>

                @if($checklist->notes)
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-navy-700">
                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-300 mb-2">Observações Gerais</label>
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $checklist->notes }}</p>
                </div>
                @endif
            </x-ui.card>
        </div>

        <!-- Itens do Checklist -->
        <x-ui.card title="Itens Verificados">
            <div class="space-y-4">
                @forelse($checklist->answers as $answer)
                    <div class="p-4 rounded-lg border {{ $answer->status === 'problem' ? 'border-danger-300 bg-danger-50 dark:border-danger-700 dark:bg-danger-900/20' : ($answer->status === 'attention' ? 'border-warning-300 bg-warning-50 dark:border-warning-700 dark:bg-warning-900/20' : 'border-success-300 bg-success-50 dark:border-success-700 dark:bg-success-900/20') }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $answer->checklistItem->name }}</h4>
                                @if($answer->checklistItem->description)
                                    <p class="text-xs text-gray-600 dark:text-navy-300 mt-1">{{ $answer->checklistItem->description }}</p>
                                @endif
                                @if($answer->notes)
                                    <div class="mt-2 p-2 bg-white dark:bg-navy-900 rounded border border-gray-200 dark:border-navy-700">
                                        <p class="text-xs font-medium text-gray-700 dark:text-navy-200">Observação:</p>
                                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $answer->notes }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                @if($answer->status === 'ok')
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400">
                                        <x-icon name="check" class="w-4 h-4" />
                                        OK
                                    </span>
                                @elseif($answer->status === 'attention')
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400">
                                        <x-icon name="exclamation" class="w-4 h-4" />
                                        Atenção
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                                        <x-icon name="alert" class="w-4 h-4" />
                                        Problema
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-navy-300 text-center py-4">Nenhum item verificado.</p>
                @endforelse
            </div>
        </x-ui.card>

        <!-- Informações de Aprovação -->
        @if($checklist->approver_id)
        <x-ui.card title="Análise do Gestor">
            @php($approvalItems=[
                ['label'=>'Analisado por','value'=>$checklist->approver->name,'bold'=>true],
                ['label'=>'Data/Hora','value'=>$checklist->approved_at->format('d/m/Y H:i')],
            ])
            <x-ui.detail-list :items="$approvalItems" />

            @if($checklist->approver_comment)
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-navy-700">
                <label class="block text-sm font-medium text-gray-700 dark:text-navy-300 mb-2">Comentário</label>
                <div class="p-4 bg-gray-50 dark:bg-navy-800 rounded-lg border border-gray-200 dark:border-navy-700">
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $checklist->approver_comment }}</p>
                </div>
            </div>
            @endif
        </x-ui.card>
        @endif

        <!-- Ações de Aprovação/Rejeição -->
        @if($checklist->isPending() && (auth()->user()->isGeneralManager() || (auth()->user()->isManager() && $checklist->run->vehicle->secretariat_id === auth()->user()->secretariat_id)))
        <x-ui.card title="Ações do Gestor">
            <p class="text-sm text-gray-600 dark:text-navy-300 mb-6">Analise o checklist e tome uma decisão. Um comentário é obrigatório.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Aprovar -->
                <form method="POST" action="{{ route('checklists.approve', $checklist) }}" x-data="{ comment: '' }">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="approve_comment" class="block text-sm font-medium text-gray-700 dark:text-navy-300 mb-2">Comentário de Aprovação *</label>
                            <textarea
                                name="comment"
                                id="approve_comment"
                                x-model="comment"
                                rows="4"
                                required
                                placeholder="Descreva as ações que serão tomadas ou orientações..."
                                class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-white shadow-sm focus:border-success-500 focus:ring-success-500"></textarea>
                        </div>
                        <button
                            type="submit"
                            :disabled="comment.trim().length === 0"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-success-600 hover:bg-success-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-md shadow-sm transition-colors duration-200">
                            <x-icon name="check" class="w-5 h-5" />
                            <span>Aprovar e Encaminhar</span>
                        </button>
                    </div>
                </form>

                <!-- Rejeitar -->
                <form method="POST" action="{{ route('checklists.reject', $checklist) }}" x-data="{ comment: '' }">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="reject_comment" class="block text-sm font-medium text-gray-700 dark:text-navy-300 mb-2">Motivo da Rejeição *</label>
                            <textarea
                                name="comment"
                                id="reject_comment"
                                x-model="comment"
                                rows="4"
                                required
                                placeholder="Explique por que a solicitação foi rejeitada..."
                                class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-white shadow-sm focus:border-danger-500 focus:ring-danger-500"></textarea>
                        </div>
                        <button
                            type="submit"
                            :disabled="comment.trim().length === 0"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-danger-600 hover:bg-danger-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-md shadow-sm transition-colors duration-200">
                            <x-icon name="x" class="w-5 h-5" />
                            <span>Rejeitar Solicitação</span>
                        </button>
                    </div>
                </form>
            </div>
        </x-ui.card>
        @endif
    </div>
</x-app-layout>
