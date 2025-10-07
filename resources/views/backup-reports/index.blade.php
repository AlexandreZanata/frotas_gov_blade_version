<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Relatórios de Backup" subtitle="Visualizar e gerenciar backups" hide-title-mobile icon="document" />
    </x-slot>

    <div class="space-y-6">
        <x-ui.card>
            <x-ui.searchable-table
                :headers="['Entidade', 'Nome', 'Arquivo', 'Tamanho', 'Usuário', 'Data', 'Ações']"
                :searchable="true"
                search-placeholder="Pesquisar por nome, tipo, arquivo ou usuário..."
                :search-value="$search ?? ''"
                :search-route="route('backup-reports.index')"
                :pagination="$backups">

                @forelse($backups as $backup)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/50 transition">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                {{ $backup->entity_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $backup->entity_name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-navy-300">{{ $backup->file_name }}</td>
                        <td class="px-4 py-3 text-sm">{{ $backup->file_size_formatted }}</td>
                        <td class="px-4 py-3 text-sm">{{ $backup->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex flex-col">
                                <span>{{ $backup->created_at->format('d/m/Y') }}</span>
                                <span class="text-xs text-gray-500 dark:text-navy-400">{{ $backup->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('backup-reports.download', $backup) }}"
                                   class="inline-flex items-center justify-center h-7 w-7 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 transition"
                                   title="Download">
                                    <x-icon name="download" class="w-4 h-4" />
                                </a>

                                <x-ui.confirm-form
                                    :action="route('backup-reports.destroy', $backup)"
                                    method="DELETE"
                                    message="Tem certeza que deseja excluir este relatório de backup? O arquivo PDF será permanentemente removido."
                                    title="Excluir Relatório"
                                    icon="trash"
                                    variant="danger">
                                    Excluir
                                </x-ui.confirm-form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-navy-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-400 dark:text-navy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">Nenhum relatório de backup encontrado</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-ui.searchable-table>
        </x-ui.card>

        @if($backups->isNotEmpty())
            <x-ui.card title="Informações sobre Backups">
                <div class="text-sm text-gray-600 dark:text-navy-300 space-y-2">
                    <p>• Os backups são gerados automaticamente quando você exclui registros importantes do sistema</p>
                    <p>• Cada backup contém todos os dados relacionados ao registro excluído em formato PDF</p>
                    <p>• Você pode baixar os backups a qualquer momento clicando no ícone de download</p>
                    <p>• Backups antigos podem ser excluídos manualmente quando não forem mais necessários</p>
                </div>
            </x-ui.card>
        @endif
    </div>
</x-app-layout>

