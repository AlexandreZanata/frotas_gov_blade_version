<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Categorias de Veículos" subtitle="Classificações" hide-title-mobile icon="category" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('vehicle-categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow">
            <x-icon name="plus" class="w-4 h-4" /> <span>Nova</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome','Criada em','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por nome..."
            :search-value="$search ?? ''"
            :pagination="$categories">
            @forelse($categories as $c)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $c->name }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500 dark:text-navy-200">{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('vehicle-categories.edit',$c)" icon="edit" title="Editar" variant="info" />
                            <x-ui.confirm-form
                                :action="route('vehicle-categories.destroy',$c)"
                                method="DELETE"
                                message="⚠️ ATENÇÃO: EXCLUSÃO PERMANENTE

Ao excluir esta categoria, ela será removida permanentemente do sistema.

IMPORTANTE: Veículos associados a esta categoria NÃO serão excluídos, mas perderão a referência de categoria.

Esta ação NÃO PODE SER DESFEITA."
                                title="Excluir Categoria"
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
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhuma categoria cadastrada.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
