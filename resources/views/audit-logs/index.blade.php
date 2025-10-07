<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Logs de Auditoria"
            subtitle="Registros de todas as ações realizadas no sistema"
            hide-title-mobile
        />
    </x-slot>

    <x-ui.card>
        <!-- Novo Componente de Filtros de Auditoria -->
        <x-ui.audit-filter
            :types="$types"
            :selectedType="$type"
            :selectedAction="$action"
            :searchValue="$search"
        />

        <!-- Tabela com Paginação -->
        <x-ui.table
            :headers="['Data/Hora', 'Usuário', 'Ação', 'Tipo', 'Descrição', 'Ações']"
            :searchable="false"
            :pagination="$logs">

            @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ $log->user->name ?? 'Sistema' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($log->action === 'created')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                Criado
                            </span>
                        @elseif($log->action === 'updated')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                Atualizado
                            </span>
                        @elseif($log->action === 'deleted')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                Excluído
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ class_basename($log->auditable_type) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        {{ Str::limit($log->description, 50) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-ui.action-icon
                            :href="route('audit-logs.show', $log)"
                            icon="eye"
                            title="Ver Detalhes"
                            variant="primary"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <x-icon name="clipboard" class="h-12 w-12 text-gray-400 mb-3" />
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum log encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if(request()->hasAny(['search', 'type', 'action']))
                                    Tente ajustar os filtros de busca.
                                @else
                                    Ainda não há registros de auditoria no sistema.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
