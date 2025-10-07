<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Veículo" subtitle="Atualização de dados">
            <x-slot name="actions">
                <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Voltar</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card title="Informações do Veículo">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('vehicles._form')
        </form>
    </x-ui.card>
</x-app-layout>
