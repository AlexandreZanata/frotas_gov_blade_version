<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Editar Veículo de Lixo"
            subtitle="Atualize as informações do veículo"
            hide-title-mobile
            icon="truck"
        />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('admin.garbage-vehicles.update', $garbageVehicle) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <x-input-label for="name" value="Nome do Veículo *" />
                    <x-text-input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        :value="old('name', $garbageVehicle->vehicle->name)"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('admin.garbage-vehicles.index') }}">
                        <x-secondary-button type="button">
                            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                            Cancelar
                        </x-secondary-button>
                    </a>

                    <x-primary-button type="submit">
                        <x-icon name="save" class="w-4 h-4 mr-2" />
                        Atualizar Veículo
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
