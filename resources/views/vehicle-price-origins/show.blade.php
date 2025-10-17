<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes do Patrimônio" subtitle="Visualização" hide-title-mobile icon="currency-dollar" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-price-origins.index')" icon="arrow-left" title="Voltar" variant="neutral" />
        <a href="{{ route('vehicle-price-origins.edit', $vehiclePriceOrigin) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium">
            <x-icon name="edit" class="w-4 h-4" /> <span>Editar</span>
        </a>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-2">
        <x-ui.card title="Informações do Patrimônio">
            @php($items=[
                ['label'=>'Veículo','value'=>e($vehiclePriceOrigin->vehicle->name),'bold'=>true],
                ['label'=>'Placa','value'=>strtoupper($vehiclePriceOrigin->vehicle->plate)],
                ['label'=>'Valor de Aquisição','value'=>$vehiclePriceOrigin->formatted_amount,'bold'=>true,'color'=>'text-green-600 dark:text-green-400'],
                ['label'=>'Data de Aquisição','value'=>$vehiclePriceOrigin->formatted_acquisition_date],
                ['label'=>'Tipo de Aquisição','value'=>e($vehiclePriceOrigin->acquisitionType->name)],
            ])
            <x-ui.detail-list :items="$items" />
        </x-ui.card>

        <x-ui.card title="Informações do Veículo">
            @php($items=[
                ['label'=>'Marca','value'=>e($vehiclePriceOrigin->vehicle->brand->name ?? '-')],
                ['label'=>'Ano/Modelo','value'=>e($vehiclePriceOrigin->vehicle->model_year)],
                ['label'=>'Categoria','value'=>e($vehiclePriceOrigin->vehicle->category->name ?? '-')],
                ['label'=>'Prefixo','value'=>e($vehiclePriceOrigin->vehicle->prefix->name ?? '-')],
                ['label'=>'Secretaria','value'=>e($vehiclePriceOrigin->vehicle->secretariat->name ?? '-')],
            ])
            <x-ui.detail-list :items="$items" />
        </x-ui.card>
    </div>
</x-app-layout>
