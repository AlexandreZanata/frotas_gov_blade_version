<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Checklists" subtitle="Gestão de checklists dos veículos" hide-title-mobile icon="clipboard-check" />
    </x-slot>

    <x-slot name="pageActions">
        @if(auth()->user()->isManager())
        <a href="{{ route('checklists.pending') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-warning-600 hover:bg-warning-700 text-white text-sm font-medium shadow transition">
            <x-icon name="bell" class="w-4 h-4" />
            <span>Pendentes</span>
        </a>
        @endif
    </x-slot>

    <x-ui.card>
        <!-- Filtros -->
        <div class="mb-6 p-4 bg-gray-50 dark:bg-navy-800 rounded-lg">
            <form method="GET" action="{{ route('checklists.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Busca -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-100 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Placa, prefixo ou usuário..."
                           class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-100 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Todos</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Aprovado</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                    </select>
                </div>

                <!-- Defeitos -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-100 mb-1">Defeitos</label>
                    <select name="has_defects" class="w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Todos</option>
                        <option value="1" {{ $hasDefects === '1' ? 'selected' : '' }}>Com Defeitos</option>
                        <option value="0" {{ $hasDefects === '0' ? 'selected' : '' }}>Sem Defeitos</option>
                    </select>
                </div>

                <!-- Botões -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md text-sm font-medium shadow transition">
                        <x-icon name="filter" class="w-4 h-4" />
                        Filtrar
                    </button>
                    <a href="{{ route('checklists.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-navy-600 dark:hover:bg-navy-500 text-gray-700 dark:text-white rounded-md text-sm font-medium shadow transition">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <x-ui.table
            :headers="['Data','Veículo','Usuário','Secretaria','Defeitos','Status','Ações']"
            :pagination="$checklists"
            :searchable="false">
            @forelse($checklists as $checklist)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $checklist->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500 dark:text-navy-300">{{ $checklist->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-4 py-2 font-medium">
                        @if($checklist->run->vehicle->prefix)
                            <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ $checklist->run->vehicle->prefix->abbreviation }}</span>
                        @endif
                        <span class="text-gray-900 dark:text-white">{{ $checklist->run->vehicle->plate }}</span>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                        {{ $checklist->user->name }}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-navy-300">
                        {{ $checklist->run->vehicle->secretariat->name ?? '-' }}
                    </td>
                    <td class="px-4 py-2">
                        @if($checklist->has_defects)
                            @php
                                $problemCount = $checklist->answers()->where('status', 'problem')->count();
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                                <x-icon name="alert" class="w-3 h-3" />
                                {{ $problemCount }} {{ $problemCount === 1 ? 'Problema' : 'Problemas' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400">
                                <x-icon name="check" class="w-3 h-3" />
                                OK
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if($checklist->approval_status === 'pending')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400">
                                Pendente
                            </span>
                        @elseif($checklist->approval_status === 'approved')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400">
                                Aprovado
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                                Rejeitado
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <x-ui.action-icon :href="route('checklists.show', $checklist)" icon="eye" title="Ver Detalhes" variant="primary" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                        Nenhum checklist encontrado.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
