<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Categoria" subtitle="Cadastrar categoria de veículo" hide-title-mobile
                          icon="category"/>
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-categories.index')" icon="arrow-left" title="Voltar" variant="neutral"/>
    </x-slot>

    <x-ui.card title="Dados da Categoria">
        @php($vehicleCategory = new \App\Models\Vehicle\VehicleCategory())
        <form action="{{ route('vehicle-categories.store') }}" method="POST" class="space-y-6">
            @include('vehicle_categories._form')
        </form>
    </x-ui.card>
</x-app-layout>
