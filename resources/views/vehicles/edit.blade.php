<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Veículo" subtitle="Atualização de dados" hide-title-mobile icon="car" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicles.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações do Veículo">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('vehicles._form')
        </form>
    </x-ui.card>
</x-app-layout>
