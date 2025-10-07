<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Detalhes do Log"
            subtitle="Informações detalhadas da ação registrada"
            hide-title-mobile
            icon="clipboard"
        />
    </x-slot>

    <x-slot name="pageActions">
        <x-ui.action-icon
            :href="route('audit-logs.index')"
            icon="arrow-left"
            title="Voltar"
            variant="neutral"
        />
    </x-slot>

    <div class="space-y-6">
        <!-- Informações Gerais -->
        <x-ui.card title="Informações Gerais">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data/Hora</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $auditLog->created_at->format('d/m/Y H:i:s') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuário</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $auditLog->user->name ?? 'Sistema' }}
                        @if($auditLog->user)
                            <span class="text-gray-500 dark:text-gray-400">({{ $auditLog->user->email }})</span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ação</dt>
                    <dd class="mt-1">
                        @if($auditLog->action === 'created')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                Criado
                            </span>
                        @elseif($auditLog->action === 'updated')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                Atualizado
                            </span>
                        @elseif($auditLog->action === 'deleted')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                Excluído
                            </span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Registro</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ class_basename($auditLog->auditable_type) }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID do Registro</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                        {{ $auditLog->auditable_id }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Endereço IP</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                        {{ $auditLog->ip_address ?? 'N/A' }}
                    </dd>
                </div>

                @if($auditLog->description)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ $auditLog->description }}
                    </dd>
                </div>
                @endif

                @if($auditLog->user_agent)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Navegador/Dispositivo</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono break-all">
                        {{ $auditLog->user_agent }}
                    </dd>
                </div>
                @endif
            </dl>
        </x-ui.card>

        <!-- Valores Antigos -->
        @php
            $oldValues = is_array($auditLog->old_values) ? $auditLog->old_values : (is_string($auditLog->old_values) ? json_decode($auditLog->old_values, true) : []);
        @endphp
        @if($oldValues && count($oldValues) > 0)
        <x-ui.card title="Valores Anteriores">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-navy-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Campo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Valor
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($oldValues as $field => $value)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ ucfirst(str_replace('_', ' ', $field)) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if(is_array($value))
                                        <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @elseif(is_bool($value))
                                        {{ $value ? 'Sim' : 'Não' }}
                                    @elseif(is_null($value))
                                        <span class="text-gray-400 italic">null</span>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
        @endif

        <!-- Valores Novos -->
        @php
            $newValues = is_array($auditLog->new_values) ? $auditLog->new_values : (is_string($auditLog->new_values) ? json_decode($auditLog->new_values, true) : []);
        @endphp
        @if($newValues && count($newValues) > 0)
        <x-ui.card title="Valores Novos">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-navy-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Campo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Valor
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($newValues as $field => $value)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ ucfirst(str_replace('_', ' ', $field)) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if(is_array($value))
                                        <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @elseif(is_bool($value))
                                        {{ $value ? 'Sim' : 'Não' }}
                                    @elseif(is_null($value))
                                        <span class="text-gray-400 italic">null</span>
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
        @endif
    </div>
</x-app-layout>
