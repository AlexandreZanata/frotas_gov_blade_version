<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Gerenciamento de Veículos de Lixo"
            subtitle="Veículos disponíveis para coleta de lixo"
            hide-title-mobile
            icon="truck"
        />
    </x-slot>

    <x-ui.card>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">Lista de Veículos de Lixo</h3>
                <p class="text-sm text-gray-500 dark:text-navy-300">Gerencie os veículos disponíveis para coleta</p>
            </div>
            <a href="{{ route('admin.garbage-vehicles.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Adicionar Veículo</span>
            </a>
        </div>

        <x-ui.table
            :headers="['Prefixo', 'Nome', 'Placa', 'Categoria', 'Ações']"
            :searchable="true"
            search-placeholder="Pesquisar veículos..."
        >
            @forelse($vehicles as $vehicle)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2">
                        <div class="text-sm font-medium text-gray-900 dark:text-navy-50">
                            {{ $vehicle->vehicle->prefix->name ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            {{ $vehicle->vehicle->name }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            {{ $vehicle->vehicle->plate }}
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm text-gray-900 dark:text-navy-50">
                            {{ $vehicle->vehicle->category->name ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <!-- Excluir -->
                            <form action="{{ route('admin.garbage-vehicles.destroy', $vehicle) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Tem certeza que deseja remover este veículo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1 rounded-md text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-navy-600 transition"
                                        title="Remover Veículo">
                                    <x-icon name="trash" class="w-5 h-5" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhum veículo de lixo encontrado.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $vehicles->links() }}
        </div>
    </x-ui.card>
</x-app-layout>
