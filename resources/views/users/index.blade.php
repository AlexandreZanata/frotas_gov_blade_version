<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Usuários" subtitle="Gestão de usuários do sistema" hide-title-mobile icon="users" />
    </x-slot>
    <x-slot name="pageActions">
        @if(auth()->user()->isManager())
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Novo Usuário</span>
            </a>
        @endif
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome','E-mail','CPF','Role','Secretaria','Status','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por nome, email, CPF..."
            :search-value="$search ?? ''"
            :pagination="$users">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->cpf }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $user->role->display_name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $user->secretariat->name ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @if($user->status === 'active')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Ativo
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Inativo
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if(auth()->user()->canManage($user))
                                <x-ui.action-icon :href="route('users.edit', $user)" icon="edit" title="Editar" variant="info" />

                                @if(auth()->id() !== $user->id)
                                    <x-ui.confirm-form
                                        :action="route('users.destroy', $user)"
                                        method="DELETE"
                                        message="⚠️ ATENÇÃO: EXCLUSÃO DE USUÁRIO

Ao excluir este usuário, todos os dados associados podem ser afetados.

Esta ação NÃO PODE SER DESFEITA."
                                        title="Excluir Usuário"
                                        icon="trash"
                                        variant="danger"
                                        :require-backup="true"
                                        :require-confirmation-text="true">
                                        Excluir
                                    </x-ui.confirm-form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhum usuário encontrado.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
