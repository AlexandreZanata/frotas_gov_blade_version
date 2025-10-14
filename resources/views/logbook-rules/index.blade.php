{{-- resources/views/logbook-rules/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Regras de Quilometragem" subtitle="Gestão de limites de km para corridas" hide-title-mobile icon="cog" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('logbook-rules.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Nova Regra</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome', 'Tipo', 'Alvo', 'Valor/Fórmula', 'Status', 'Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por nome, tipo ou alvo..."
            :search-value="$search ?? ''"
            :pagination="$rules">
            @forelse($rules as $rule)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $rule->name }}</td>
                    <td class="px-4 py-2">
                        @if($rule->rule_type === 'fixed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                Valor Fixo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Fórmula
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $rule->target_name }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                        @if($rule->rule_type === 'fixed')
                            <span class="font-medium">{{ $rule->fixed_value }} km</span>
                        @else
                            @if($rule->formula_type === 'daily_average_plus_fixed')
                                Média Diária + <span class="font-medium">{{ $rule->formula_value }} km</span>
                            @else
                                Média Diária + <span class="font-medium">{{ $rule->formula_value }}%</span>
                            @endif
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($rule->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Ativa
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                Inativa
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('logbook-rules.edit', $rule)" icon="edit" title="Editar" variant="info" />
                            <x-ui.confirm-form
                                :action="route('logbook-rules.destroy', $rule)"
                                method="DELETE"
                                message="Tem certeza que deseja excluir esta regra de quilometragem?"
                                title="Excluir Regra"
                                icon="trash"
                                variant="danger">
                                Excluir
                            </x-ui.confirm-form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                            <x-icon name="cog" class="w-12 h-12 mb-4 text-gray-400" />
                            <p class="text-lg font-medium mb-2">Nenhuma regra cadastrada</p>
                            <p class="text-sm mb-4">Comece criando sua primeira regra de quilometragem</p>
                            <a href="{{ route('logbook-rules.create') }}"
                               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors inline-flex items-center gap-2">
                                <x-icon name="plus" class="w-4 h-4" />
                                Criar Primeira Regra
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    @if($rules->isNotEmpty())
        <x-ui.card title="Informações sobre Regras de Quilometragem">
            <div class="text-sm text-gray-600 dark:text-navy-300 space-y-2">
                <p>• As regras definem o limite máximo de km permitido para uma corrida</p>
                <p>• Podem ser aplicadas globalmente ou para alvos específicos (categoria, usuário ou veículo)</p>
                <p>• Use valores fixos ou fórmulas baseadas na média diária do veículo</p>
                <p>• As regras são validadas automaticamente ao iniciar uma nova corrida</p>
            </div>
        </x-ui.card>
    @endif
</x-app-layout>
