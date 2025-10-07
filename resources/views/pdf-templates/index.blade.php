<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Modelos de PDF" subtitle="Templates personalizados" hide-title-mobile icon="document" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('pdf-templates.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow">
            <x-icon name="plus" class="w-4 h-4" /> <span>Novo Template</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome', 'Criado em', 'Atualizado em', 'Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por nome..."
            :search-value="$search ?? ''"
            :pagination="$templates">
            @forelse($templates as $template)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $template->name }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500 dark:text-navy-200">{{ $template->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500 dark:text-navy-200">{{ $template->updated_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('pdf-templates.show', $template)" icon="eye" title="Visualizar" variant="primary" />
                            <x-ui.action-icon :href="route('pdf-templates.edit', $template)" icon="edit" title="Editar" variant="info" />
                            <x-ui.confirm-form
                                :action="route('pdf-templates.destroy', $template)"
                                method="DELETE"
                                message="⚠️ ATENÇÃO: EXCLUSÃO DE TEMPLATE

Ao excluir este template, ele será permanentemente removido do sistema.

Esta ação NÃO PODE SER DESFEITA."
                                title="Excluir Template"
                                icon="trash"
                                variant="danger"
                                :require-confirmation-text="true">
                                Excluir
                            </x-ui.confirm-form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum template cadastrado.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>

