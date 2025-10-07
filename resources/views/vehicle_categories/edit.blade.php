<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Categoria" subtitle="Atualizar dados da categoria">
            <x-slot name="actions">
                <a href="{{ route('vehicle-categories.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Voltar</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card title="Dados da Categoria">
        <form action="{{ route('vehicle-categories.update', $vehicleCategory) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('vehicle_categories._form')
        </form>
    </x-ui.card>
</x-app-layout>
