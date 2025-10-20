<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Gerenciamento de Bairros"
            subtitle="Bairros disponíveis para coleta de lixo"
            hide-title-mobile
            icon="map-pin"
        />
    </x-slot>

    <x-ui.card>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">Lista de Bairros</h3>
                <p class="text-sm text-gray-500 dark:text-navy-300">Gerencie os bairros disponíveis para coleta</p>
            </div>
            <a href="{{ route('admin.garbage-neighborhoods.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Adicionar Bairro</span>
            </a>
        </div>

        <x-ui.table
            :headers="['Nome', 'Ações']"
            :searchable="true"
            search-placeholder="Pesquisar bairros..."
        >
            @forelse($neighborhoods as $neighborhood)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="text-sm font-medium text-gray-900 dark:text-navy-50">
                            {{ $neighborhood->name }}
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <!-- Editar -->
                            <a href="{{ route('admin.garbage-neighborhoods.edit', $neighborhood) }}"
                               class="p-1 rounded-md text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-navy-600 transition"
                               title="Editar Bairro">
                                <x-icon name="pencil" class="w-5 h-5" />
                            </a>

                            <!-- Excluir -->
                            <form action="{{ route('admin.garbage-neighborhoods.destroy', $neighborhood) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir este bairro?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1 rounded-md text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-navy-600 transition"
                                        title="Excluir Bairro">
                                    <x-icon name="trash" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhum bairro encontrado.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $neighborhoods->links() }}
        </div>
    </x-ui.card>
</x-app-layout>
