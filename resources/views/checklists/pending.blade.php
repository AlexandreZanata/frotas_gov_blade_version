<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Notificações de Checklists" subtitle="Checklists e relatórios de defeitos pendentes de aprovação" hide-title-mobile icon="bell" />
    </x-slot>

    <!-- Checklists com Defeitos Pendentes -->
    <x-ui.card class="mb-6">
        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-navy-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <x-icon name="clipboard-check" class="w-5 h-5 text-warning-600" />
                Checklists com Defeitos
            </h3>
            <p class="text-sm text-gray-600 dark:text-navy-300 mt-1">Checklists que reportaram problemas e aguardam sua análise</p>
        </div>

        <x-ui.table
            :headers="['Data','Veículo','Motorista','Secretaria','Problemas','Ações']"
            :pagination="$pendingChecklists"
            :searchable="false">
            @forelse($pendingChecklists as $checklist)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $checklist->created_at->format('d/m/Y') }}</div>
                            <div class="text-gray-500 dark:text-navy-300">{{ $checklist->created_at->format('H:i') }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">
                                @if($checklist->run->vehicle->prefix)
                                    <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ $checklist->run->vehicle->prefix->abbreviation }}</span>
                                @endif
                                {{ $checklist->run->vehicle->plate }}
                            </div>
                            <div class="text-gray-500 dark:text-navy-300">{{ $checklist->run->vehicle->name }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $checklist->user->name }}</div>
                            <div class="text-gray-500 dark:text-navy-300">{{ $checklist->user->email }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-navy-300">
                        {{ $checklist->run->vehicle->secretariat->name ?? '-' }}
                    </td>
                    <td class="px-4 py-2">
                        @php
                            $problemCount = $checklist->answers()->where('status', 'problem')->count();
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                            <x-icon name="alert" class="w-3 h-3" />
                            {{ $problemCount }} {{ $problemCount === 1 ? 'Problema' : 'Problemas' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <a href="{{ route('checklists.show', $checklist) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-md shadow transition">
                            <x-icon name="eye" class="w-4 h-4" />
                            Analisar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <x-icon name="check-circle" class="w-12 h-12 text-success-500" />
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Nenhum checklist pendente</p>
                            <p class="text-xs text-gray-500 dark:text-navy-300">Todos os checklists com defeitos foram analisados</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>

    <!-- Relatórios de Defeitos Pendentes -->
    <x-ui.card>
        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-navy-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <x-icon name="alert" class="w-5 h-5 text-danger-600" />
                Fichas de Comunicação de Defeitos
            </h3>
            <p class="text-sm text-gray-600 dark:text-navy-300 mt-1">Relatórios de defeitos que aguardam sua análise</p>
        </div>

        <x-ui.table
            :headers="['Data','Veículo','Motorista','Secretaria','Status','Ações']"
            :pagination="$pendingDefectReports"
            :searchable="false">
            @forelse($pendingDefectReports as $defectReport)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $defectReport->created_at->format('d/m/Y') }}</div>
                            <div class="text-gray-500 dark:text-navy-300">{{ $defectReport->created_at->format('H:i') }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">
                                @if($defectReport->vehicle->prefix)
                                    <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ $defectReport->vehicle->prefix->abbreviation }}</span>
                                @endif
                                {{ $defectReport->vehicle->plate }}
                            </div>
                            <div class="text-gray-500 dark:text-navy-300">{{ $defectReport->vehicle->name }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $defectReport->user->name }}</div>
                            <div class="text-gray-500 dark:text-navy-300">{{ $defectReport->user->email }}</div>
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600 dark:text-navy-300">
                        {{ $defectReport->vehicle->secretariat->name ?? '-' }}
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400">
                            Aberto
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <a href="#"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-md shadow transition">
                            <x-icon name="eye" class="w-4 h-4" />
                            Analisar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <x-icon name="check-circle" class="w-12 h-12 text-success-500" />
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Nenhum relatório pendente</p>
                            <p class="text-xs text-gray-500 dark:text-navy-300">Todos os relatórios de defeitos foram analisados</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
