<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes do Log" subtitle="Informações detalhadas da alteração" hide-title-mobile icon="clipboard-list" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('audit-logs.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <div class="space-y-6">
        <!-- Informações Gerais -->
        <x-ui.card title="Informações Gerais">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data e Hora</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usuário</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $auditLog->user ? $auditLog->user->name : 'Sistema' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ação</label>
                    <p class="mt-1">
                        @if($auditLog->action == 'created')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Criação
                            </span>
                        @elseif($auditLog->action == 'updated')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Atualização
                            </span>
                        @elseif($auditLog->action == 'deleted')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Exclusão
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ class_basename($auditLog->auditable_type) }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $auditLog->description }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $auditLog->ip_address ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID do Registro</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $auditLog->auditable_id }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Valores Anteriores -->
        @if($auditLog->old_values)
            <x-ui.card title="Valores Anteriores">
                <div class="bg-gray-50 dark:bg-navy-900 rounded-lg p-4">
                    <pre class="text-sm text-gray-900 dark:text-white overflow-x-auto">{{ json_encode(json_decode($auditLog->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </x-ui.card>
        @endif

        <!-- Novos Valores -->
        @if($auditLog->new_values)
            <x-ui.card title="Novos Valores">
                <div class="bg-gray-50 dark:bg-navy-900 rounded-lg p-4">
                    <pre class="text-sm text-gray-900 dark:text-white overflow-x-auto">{{ json_encode(json_decode($auditLog->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </x-ui.card>
        @endif

        <!-- Alterações (apenas para updated) -->
        @if($auditLog->action == 'updated' && $auditLog->old_values && $auditLog->new_values)
            <x-ui.card title="Alterações Realizadas">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-navy-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Campo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Valor Anterior</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Novo Valor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $oldValues = json_decode($auditLog->old_values, true);
                                $newValues = json_decode($auditLog->new_values, true);
                            @endphp
                            @foreach($newValues as $key => $newValue)
                                @if(isset($oldValues[$key]) && $oldValues[$key] != $newValue)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $key }}</td>
                                        <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">
                                            {{ is_array($oldValues[$key]) ? json_encode($oldValues[$key]) : $oldValues[$key] }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400">
                                            {{ is_array($newValue) ? json_encode($newValue) : $newValue }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Logs de Auditoria" subtitle="Histórico de alterações no sistema" hide-title-mobile icon="clipboard-list" />
    </x-slot>

    <x-ui.card>
        <!-- Filtros -->
        <div class="mb-6 space-y-4">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Pesquisa -->
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Pesquisar" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" placeholder="Pesquisar por descrição, usuário..." />
                    </div>

                    <!-- Ação -->
                    <div>
                        <x-input-label for="action" value="Ação" />
                        <select id="action" name="action" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                            <option value="">Todas</option>
                            <option value="created" {{ $action == 'created' ? 'selected' : '' }}>Criação</option>
                            <option value="updated" {{ $action == 'updated' ? 'selected' : '' }}>Atualização</option>
                            <option value="deleted" {{ $action == 'deleted' ? 'selected' : '' }}>Exclusão</option>
                        </select>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <x-input-label for="type" value="Tipo" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                            <option value="">Todos</option>
                            @foreach($types as $typeOption)
                                <option value="{{ $typeOption['value'] }}" {{ $type == $typeOption['value'] ? 'selected' : '' }}>
                                    {{ $typeOption['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <x-primary-button>
                        <x-icon name="search" class="w-4 h-4 mr-2" />
                        Filtrar
                    </x-primary-button>
                    <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabela de Logs -->
        <x-ui.table
            :headers="['Data/Hora','Usuário','Ação','Tipo','Descrição','Ações']"
            :pagination="$logs">
            @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-3 text-sm whitespace-nowrap">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $log->user ? $log->user->name : 'Sistema' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($log->action == 'created')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Criação
                            </span>
                        @elseif($log->action == 'updated')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                Atualização
                            </span>
                        @elseif($log->action == 'deleted')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Exclusão
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                {{ ucfirst($log->action) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <span class="font-mono text-xs">{{ class_basename($log->auditable_type) }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $log->description }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right">
                        <a href="{{ route('audit-logs.show', $log) }}"
                           class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/40 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 transition"
                           title="Ver Detalhes">
                            <x-icon name="eye" class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <x-icon name="clipboard-list" class="w-12 h-12 text-gray-400" />
                            <p>Nenhum log de auditoria encontrado.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>

