<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Patrimônio" subtitle="Cadastro de valor de aquisição do veículo" hide-title-mobile icon="currency-dollar" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-price-origins.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações do Patrimônio">
        <form action="{{ route('vehicle-price-origins.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('vehicle-price-origins._form')
        </form>
    </x-ui.card>
</x-app-layout>
