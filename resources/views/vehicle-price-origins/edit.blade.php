<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Patrimônio" subtitle="Atualização de valor de aquisição" hide-title-mobile icon="currency-dollar" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-price-origins.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações do Patrimônio">
        <form action="{{ route('vehicle-price-origins.update', $vehiclePriceOrigin) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('vehicle-price-origins._form')
        </form>
    </x-ui.card>
</x-app-layout>
