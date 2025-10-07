<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Prefixos" subtitle="Identificadores de veículos">
            <x-slot name="actions">
                <a href="{{ route('prefixes.create') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Novo
                </a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card>
        <x-ui.table :headers="['Nome','Criado em','Ações']">
            @forelse($prefixes as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $p->name }}</td>
                    <td class="px-4 py-2 text-xs text-gray-500 dark:text-navy-200">{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                        <a href="{{ route('prefixes.edit',$p) }}" class="text-blue-600 hover:underline text-xs">Editar</a>
                        <form action="{{ route('prefixes.destroy',$p) }}" method="POST" class="inline" onsubmit="return confirm('Confirmar exclusão?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-xs">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum prefixo cadastrado.</td>
                </tr>
            @endforelse
        </x-ui.table>
        <div class="pt-4">{{ $prefixes->links() }}</div>
    </x-ui.card>
</x-app-layout>
