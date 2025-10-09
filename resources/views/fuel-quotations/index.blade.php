<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Cotação de Combustível" subtitle="Gestão de cotações de preços" hide-title-mobile icon="trending-up" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('fuel-quotations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Nova Cotação</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome','Data','Método','Status','Criado por','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar cotações..."
            :search-value="$search ?? ''"
            :pagination="$quotations">
            @forelse($quotations as $quotation)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $quotation->name }}</td>
                    <td class="px-4 py-2 text-gray-700 dark:text-navy-200">
                        {{ $quotation->quotation_date->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-2 text-gray-700 dark:text-navy-200">
                        @if($quotation->calculation_method === 'simple_average')
                            Média Simples
                        @else
                            Personalizado
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($quotation->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Concluída
                            </span>
                        @elseif($quotation->status === 'draft')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                Rascunho
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                Cancelada
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-gray-700 dark:text-navy-200">
                        {{ $quotation->user->name }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('fuel-quotations.show', $quotation) }}"
                               title="Ver"
                               class="p-1.5 text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/20 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <form action="{{ route('fuel-quotations.destroy', $quotation) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta cotação?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Excluir"
                                        class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhuma cotação cadastrada.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>

