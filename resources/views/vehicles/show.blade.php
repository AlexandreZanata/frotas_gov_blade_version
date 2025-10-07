<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes do Veículo" subtitle="Visualização">
            <x-slot name="actions">
                <a href="{{ route('vehicles.edit',$vehicle) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium">
                    Editar
                </a>
                <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Voltar</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-2">
        <x-ui.card title="Informações Gerais">
            <dl class="divide-y divide-gray-200 dark:divide-navy-700 text-sm">
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Nome</dt><dd class="col-span-2 font-medium">{{ $vehicle->name }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Marca</dt><dd class="col-span-2">{{ $vehicle->brand }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Ano/Modelo</dt><dd class="col-span-2">{{ $vehicle->model_year }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Placa</dt><dd class="col-span-2 uppercase">{{ $vehicle->plate }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Categoria</dt><dd class="col-span-2">{{ $vehicle->category->name ?? '-' }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Prefixo</dt><dd class="col-span-2">{{ $vehicle->prefix->name ?? '-' }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Status</dt><dd class="col-span-2">{{ $vehicle->status->name ?? '-' }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Chassi</dt><dd class="col-span-2">{{ $vehicle->chassis ?: '—' }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">RENAVAM</dt><dd class="col-span-2">{{ $vehicle->renavam ?: '—' }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Registro</dt><dd class="col-span-2">{{ $vehicle->registration ?: '—' }}</dd></div>
                <div class="py-2 grid grid-cols-3 gap-2"><dt class="text-gray-500 dark:text-navy-200">Tanque (L)</dt><dd class="col-span-2">{{ $vehicle->fuel_tank_capacity }}</dd></div>
            </dl>
        </x-ui.card>
        <x-ui.card title="Ações Rápidas">
            <div class="space-y-3">
                <form action="{{ route('vehicles.destroy',$vehicle) }}" method="POST" onsubmit="return confirm('Confirmar exclusão definitiva?')" class="space-y-3">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>Excluir Veículo</x-danger-button>
                </form>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
