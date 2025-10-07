<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Categoria" subtitle="Atualizar dados da categoria" hide-title-mobile icon="category" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('vehicle-categories.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Dados da Categoria">
        <form action="{{ route('vehicle-categories.update', $vehicleCategory) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('vehicle_categories._form')
        </form>
    </x-ui.card>
</x-app-layout>
