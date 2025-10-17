<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Veículo" subtitle="Cadastro de veículo" hide-title-mobile icon="car"/>
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicles.index')" icon="arrow-left" title="Voltar" variant="neutral"/>
    </x-slot>

    <x-ui.card title="Informações Básicas">
        @php($vehicle = new \App\Models\Vehicle\Vehicle())
        <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-6">
            @include('vehicles._form')
        </form>
    </x-ui.card>
</x-app-layout>
