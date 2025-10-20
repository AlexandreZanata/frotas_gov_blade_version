<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Gerenciamento de Usuários de Lixo"
            subtitle="Configure veículos e bairros para cada usuário"
            hide-title-mobile
            icon="users"
        />
    </x-slot>

    <x-ui.card>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">Lista de Usuários de Lixo</h3>
                <p class="text-sm text-gray-500 dark:text-navy-300">Gerencie os usuários e suas permissões</p>
            </div>
            <a href="{{ route('admin.garbage-users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Adicionar Usuário</span>
            </a>
        </div>

        <x-ui.table
            :headers="['Usuário', 'Email', 'Veículos', 'Bairros', 'Ações']"
            :searchable="true"
            search-placeholder="Pesquisar usuários..."
        >
            @forelse($garbageUsers as $garbageUser)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="text-sm font-medium text-gray-900 dark:text-navy-50">
                            {{ $garbageUser->user->name }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            {{ $garbageUser->user->email }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $garbageUser->vehicles->count() }} veículos
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                {{ $garbageUser->neighborhoods->count() }} bairros
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <!-- Gerenciar Veículos -->
                            <a href="{{ route('admin.garbage-users.vehicles.edit', $garbageUser) }}"
                               class="p-1 rounded-md text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-navy-600 transition"
                               title="Gerenciar Veículos">
                                <x-icon name="truck" class="w-5 h-5" />
                            </a>

                            <!-- Gerenciar Bairros -->
                            <a href="{{ route('admin.garbage-users.neighborhoods.edit', $garbageUser) }}"
                               class="p-1 rounded-md text-green-600 hover:bg-green-100 dark:text-green-400 dark:hover:bg-navy-600 transition"
                               title="Gerenciar Bairros">
                                <x-icon name="map-pin" class="w-5 h-5" />
                            </a>

                            <!-- Ver Detalhes -->
                            <a href="{{ route('admin.garbage-users.show', $garbageUser) }}"
                               class="p-1 rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-navy-600 transition"
                               title="Ver Detalhes">
                                <x-icon name="eye" class="w-5 h-5" />
                            </a>

                            <!-- Excluir -->
                            <form action="{{ route('admin.garbage-users.destroy', $garbageUser) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1 rounded-md text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-navy-600 transition"
                                        title="Excluir Usuário">
                                    <x-icon name="trash" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhum usuário de lixo encontrado.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $garbageUsers->links() }}
        </div>
    </x-ui.card>
</x-app-layout>
