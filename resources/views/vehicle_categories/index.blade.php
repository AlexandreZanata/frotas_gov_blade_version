<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Categorias de Veículos" subtitle="Classificações">
            <x-slot name="actions">
                <a href="{{ route('vehicle-categories.create') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nova
                </a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card>
        <x-ui.table :headers="['Nome','Criada em','Ações']">
            @forelse($categories as $c)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $c->name }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500 dark:text-navy-200">{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                        <a href="{{ route('vehicle-categories.edit',$c) }}" class="text-blue-600 hover:underline text-xs">Editar</a>
                        <form action="{{ route('vehicle-categories.destroy',$c) }}" method="POST" class="inline" onsubmit="return confirm('Confirmar exclusão?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-xs">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhuma categoria cadastrada.</td>
                </tr>
            @endforelse
        </x-ui.table>
        <div class="pt-4">{{ $categories->links() }}</div>
    </x-ui.card>
</x-app-layout>
