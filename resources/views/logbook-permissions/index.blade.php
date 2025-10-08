<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Privilégios do Diário de Bordo" subtitle="Gestão de permissões" hide-title-mobile icon="shield" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('logbook-permissions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Novo</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Usuário','Escopo','Detalhes','Status','Ações']"
            :searchable="false"
            :pagination="$permissions">
            @forelse($permissions as $permission)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $permission->user->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $permission->user->email }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        @if($permission->scope === 'all')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                <x-icon name="users" class="w-3 h-3 mr-1" />
                                Todas Secretarias
                            </span>
                        @elseif($permission->scope === 'secretariat')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <x-icon name="building" class="w-3 h-3 mr-1" />
                                Secretaria
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <x-icon name="car" class="w-3 h-3 mr-1" />
                                Veículos Específicos
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($permission->scope === 'secretariat')
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $permission->secretariat->name ?? 'N/A' }}</div>
                        @elseif($permission->scope === 'vehicles')
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $permission->vehicles->count() }} veículo(s)</div>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400">Acesso total</div>
                        @endif
                        @if($permission->description)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ Str::limit($permission->description, 50) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($permission->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Ativo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                </svg>
                                Inativo
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('logbook-permissions.edit', $permission)" icon="edit" title="Editar" variant="info" />
                            <x-ui.confirm-form
                                :action="route('logbook-permissions.destroy', $permission)"
                                method="DELETE"
                                message="Tem certeza que deseja excluir esta permissão?"
                                title="Excluir Permissão"
                                icon="trash"
                                variant="danger">
                                Excluir
                            </x-ui.confirm-form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhuma permissão cadastrada.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
