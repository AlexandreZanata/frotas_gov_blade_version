<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes do Veículo" subtitle="Visualização" hide-title-mobile icon="car" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicles.index')" icon="arrow-left" title="Voltar" variant="neutral" />
        <a href="{{ route('vehicles.edit',$vehicle) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium">
            <x-icon name="edit" class="w-4 h-4" /> <span>Editar</span>
        </a>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-2">
        <x-ui.card title="Informações Gerais">
            @php($items=[
                ['label'=>'Nome','value'=>e($vehicle->name),'bold'=>true],
                ['label'=>'Marca','value'=>e($vehicle->brand)],
                ['label'=>'Ano/Modelo','value'=>e($vehicle->model_year)],
                ['label'=>'Placa','value'=>strtoupper($vehicle->plate)],
                ['label'=>'Categoria','value'=>e($vehicle->category->name ?? '-')],
                ['label'=>'Prefixo','value'=>e($vehicle->prefix->name ?? '-')],
                ['label'=>'Combustível','value'=>e($vehicle->fuelType->name ?? '—')],
                ['label'=>'Status','value'=>e($vehicle->status->name ?? '-')],
                ['label'=>'Tanque (L)','value'=>e($vehicle->fuel_tank_capacity)],
                ['label'=>'Chassi','value'=>e($vehicle->chassis ?: '—')],
                ['label'=>'RENAVAM','value'=>e($vehicle->renavam ?: '—')],
                ['label'=>'Registro','value'=>e($vehicle->registration ?: '—')],
            ])
            <x-ui.detail-list :items="$items" />
        </x-ui.card>
        <x-ui.card title="Ações Rápidas">
            <div class="space-y-3">
                <x-ui.confirm-form
                    :action="route('vehicles.destroy',$vehicle)"
                    method="DELETE"
                    message="⚠️ ATENÇÃO: EXCLUSÃO PERMANENTE

Ao excluir este veículo, TODOS os dados relacionados serão permanentemente removidos do sistema, incluindo:

• Ordens de Serviço e manutenções
• Histórico de abastecimentos
• Registro de viagens e rotas
• Multas e infrações
• Relatórios de defeitos
• Transferências entre secretarias

Esta ação NÃO PODE SER DESFEITA. Os dados não poderão ser recuperados após a exclusão.

Recomendamos fortemente gerar um backup em PDF antes de prosseguir."
                    title="Excluir Veículo Permanentemente"
                    button-class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition shadow-sm"
                    icon="trash"
                    :icon-only="false"
                    :require-backup="true"
                    :require-confirmation-text="true"
                    confirmation-text="Eu estou ciente">
                    Excluir Veículo
                </x-ui.confirm-form>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
