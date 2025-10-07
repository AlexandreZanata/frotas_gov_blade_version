<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Veículo" subtitle="Cadastro de veículo">
            <x-slot name="actions">
                <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Voltar</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card title="Informações Básicas">
        @php($vehicle = new \App\Models\Vehicle())
        <form action="{{ route('vehicles.store') }}" method="POST" class="space-y-6">
            @include('vehicles._form')
        </form>
    </x-ui.card>
</x-app-layout>
