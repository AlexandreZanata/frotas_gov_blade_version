<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Senhas Padrão" subtitle="Gerenciar senhas padrão do sistema" hide-title-mobile icon="key" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('default-passwords.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Adicionar</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome','Descrição','Status','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por nome ou descrição..."
            :search-value="$search ?? ''"
            :pagination="$passwords">
            @forelse($passwords as $password)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $password->name }}</td>
                    <td class="px-4 py-2">{{ $password->description ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @if($password->is_active)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Ativa
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                Inativa
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('default-passwords.edit', $password)" icon="edit" title="Editar" variant="info" />

                            <x-ui.confirm-form
                                :action="route('default-passwords.destroy', $password)"
                                method="DELETE"
                                message="Tem certeza que deseja excluir esta senha padrão? Esta ação não pode ser desfeita."
                                title="Excluir Senha Padrão"
                                icon="trash"
                                variant="danger">
                                Excluir
                            </x-ui.confirm-form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <x-icon name="key" class="w-12 h-12 text-gray-400" />
                            <p>Nenhuma senha padrão cadastrada.</p>
                            <a href="{{ route('default-passwords.create') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium text-sm">
                                Clique aqui para criar a primeira
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
