<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Categoria" subtitle="Cadastrar categoria de veículo">
            <x-slot name="actions">
                <a href="{{ route('vehicle-categories.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Voltar</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card title="Dados da Categoria">
        @php($vehicleCategory = new \App\Models\VehicleCategory())
        <form action="{{ route('vehicle-categories.store') }}" method="POST" class="space-y-6">
            @include('vehicle_categories._form')
        </form>
    </x-ui.card>
</x-app-layout>
