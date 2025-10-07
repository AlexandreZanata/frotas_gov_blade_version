<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Veículos" subtitle="Gestão da frota" hide-title-mobile icon="car" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Novo</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Nome','Marca','Ano','Placa','Categoria','Combustível','Status','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por nome, placa, marca ou categoria..."
            :search-value="$search ?? ''"
            :pagination="$vehicles">
            @forelse($vehicles as $v)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $v->name }}</td>
                    <td class="px-4 py-2">{{ $v->brand }}</td>
                    <td class="px-4 py-2">{{ $v->model_year }}</td>
                    <td class="px-4 py-2 uppercase tracking-wide">{{ $v->plate }}</td>
                    <td class="px-4 py-2">{{ $v->category->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $v->fuelType->name ?? '—' }}</td>
                    <td class="px-4 py-2">
                        {{-- USANDO O NOVO COMPONENTE DE STATUS COM AS NOVAS CORES --}}
                        <x-ui.status-badge :status="$v->status" />
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('vehicles.show',$v)" icon="eye" title="Ver" variant="primary" />
                            <x-ui.action-icon :href="route('vehicles.edit',$v)" icon="edit" title="Editar" variant="info" />
                            <x-ui.confirm-form
                                :action="route('vehicles.destroy',$v)"
                                method="DELETE"
                                message="⚠️ ATENÇÃO: EXCLUSÃO PERMANENTE

Ao excluir este veículo, TODOS os dados relacionados serão permanentemente removidos do sistema.

Esta ação NÃO PODE SER DESFEITA."
                                title="Excluir Veículo"
                                icon="trash"
                                variant="danger"
                                :require-backup="true"
                                :require-confirmation-text="true">
                                Excluir
                            </x-ui.confirm-form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum veículo cadastrado.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
