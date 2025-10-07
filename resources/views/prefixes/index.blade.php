<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Prefixos" subtitle="Identificadores de veículos" hide-title-mobile icon="prefix" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('prefixes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow">
            <x-icon name="plus" class="w-4 h-4" /> <span>Novo</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome','Criado em','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por nome..."
            :search-value="$search ?? ''"
            :pagination="$prefixes">
            @forelse($prefixes as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $p->name }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500 dark:text-navy-200">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('prefixes.edit',$p)" icon="edit" title="Editar" variant="info" />
                            <x-ui.confirm-form
                                :action="route('prefixes.destroy',$p)"
                                method="DELETE"
                                message="⚠️ ATENÇÃO: EXCLUSÃO PERMANENTE

Ao excluir este prefixo, ele será removido permanentemente do sistema.

IMPORTANTE: Veículos associados a este prefixo NÃO serão excluídos, mas perderão a referência de prefixo.

Esta ação NÃO PODE SER DESFEITA."
                                title="Excluir Prefixo"
                                icon="trash"
                                variant="danger"
                                :require-backup="true"
                                :require-confirmation-text="true">
                                Excluir
                            </x-ui.confirm-form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum prefixo cadastrado.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
