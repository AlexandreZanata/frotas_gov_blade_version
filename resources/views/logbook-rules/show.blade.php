{{-- resources/views/logbook-rules/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes da Regra" subtitle="Visualização" hide-title-mobile icon="cog" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook-rules.index')" icon="arrow-left" title="Voltar" variant="neutral" />
        <a href="{{ route('logbook-rules.edit', $logbookRule) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium">
            <x-icon name="edit" class="w-4 h-4" /> <span>Editar</span>
        </a>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-2">
        <x-ui.card title="Informações da Regra">
            @php($items = [
                ['label' => 'Nome', 'value' => e($logbookRule->name), 'bold' => true],
                ['label' => 'Tipo', 'value' => $logbookRule->rule_type === 'fixed' ? 'Valor Fixo' : 'Fórmula'],
                ['label' => 'Aplicação', 'value' => $logbookRule->target_name],
                ['label' => 'Status', 'value' => $logbookRule->is_active ? 'Ativa' : 'Inativa'],
                ['label' => 'Limite de KM', 'value' =>
                    $logbookRule->rule_type === 'fixed'
                        ? $logbookRule->fixed_value . ' km'
                        : ($logbookRule->formula_type === 'daily_average_plus_fixed'
                            ? 'Média Diária + ' . $logbookRule->formula_value . ' km'
                            : 'Média Diária + ' . $logbookRule->formula_value . '%')
                ],
                ['label' => 'Descrição', 'value' => e($logbookRule->description ?: '—')],
                ['label' => 'Criada em', 'value' => $logbookRule->created_at->format('d/m/Y H:i')],
                ['label' => 'Atualizada em', 'value' => $logbookRule->updated_at->format('d/m/Y H:i')],
            ])
            <x-ui.detail-list :items="$items" />
        </x-ui.card>

        <x-ui.card title="Ações">
            <div class="space-y-3">
                <form action="{{ route('logbook-rules.toggle-status', $logbookRule) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md text-sm font-semibold
                                   {{ $logbookRule->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }}
                                   text-white transition shadow-sm">
                        <x-icon name="{{ $logbookRule->is_active ? 'pause' : 'play' }}" class="w-4 h-4" />
                        {{ $logbookRule->is_active ? 'Desativar Regra' : 'Ativar Regra' }}
                    </button>
                </form>

                <x-ui.confirm-form
                    :action="route('logbook-rules.destroy', $logbookRule)"
                    method="DELETE"
                    message="Tem certeza que deseja excluir esta regra de quilometragem?"
                    title="Excluir Regra"
                    button-class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm"
                    icon="trash"
                    :icon-only="false">
                    Excluir Regra
                </x-ui.confirm-form>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
