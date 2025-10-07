<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Veículos" subtitle="Gestão da frota" :hide-title-mobile="true">
            <x-slot name="actions">
                <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Novo</span>
                </a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card>
        <x-ui.table :headers="['Nome','Marca','Ano','Placa','Categoria','Combustível','Status','Ações']">
            @forelse($vehicles as $v)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $v->name }}</td>
                    <td class="px-4 py-2">{{ $v->brand }}</td>
                    <td class="px-4 py-2">{{ $v->model_year }}</td>
                    <td class="px-4 py-2 uppercase tracking-wide">{{ $v->plate }}</td>
                    <td class="px-4 py-2">{{ $v->category->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $v->fuelType->name ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50">{{ $v->status->name ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-2 space-x-1 whitespace-nowrap">
                        <a href="{{ route('vehicles.show',$v) }}" class="inline-flex items-center justify-center h-7 w-7 rounded-md text-primary-600 hover:bg-primary-50 dark:text-navy-100 dark:hover:bg-navy-700/60" title="Ver">
                            <x-icon name="eye" class="w-4 h-4" />
                        </a>
                        <a href="{{ route('vehicles.edit',$v) }}" class="inline-flex items-center justify-center h-7 w-7 rounded-md text-blue-600 hover:bg-blue-50 dark:text-navy-100 dark:hover:bg-navy-700/60" title="Editar">
                            <x-icon name="edit" class="w-4 h-4" />
                        </a>
                        <form action="{{ route('vehicles.destroy',$v) }}" method="POST" class="inline" onsubmit="return confirm('Confirmar exclusão?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center h-7 w-7 rounded-md text-red-600 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/30" title="Excluir">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum veículo cadastrado.</td>
                </tr>
            @endforelse
        </x-ui.table>
        <div class="pt-4">{{ $vehicles->links() }}</div>
    </x-ui.card>
</x-app-layout>
