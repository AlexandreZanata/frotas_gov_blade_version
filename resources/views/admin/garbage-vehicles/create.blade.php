<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Adicionar Veículo de Lixo"
            subtitle="Vincule um veículo ao sistema de coleta"
            hide-title-mobile
            icon="truck"
        />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('admin.garbage-vehicles.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <x-input-label for="vehicle_id" value="Selecionar Veículo *" />
                    <x-ui.select name="vehicle_id" id="vehicle_id" class="mt-2" required>
                        <option value="">Selecione um veículo...</option>
                        @foreach($availableVehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                {{ $vehicle->prefix->name ?? 'N/A' }} - {{ $vehicle->name }} ({{ $vehicle->plate }})
                            </option>
                        @endforeach
                    </x-ui.select>
                    <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                    @if($availableVehicles->isEmpty())
                        <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">
                            Todos os veículos já estão vinculados ao sistema de lixo.
                        </p>
                    @endif
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('admin.garbage-vehicles.index') }}">
                        <x-secondary-button type="button">
                            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                            Cancelar
                        </x-secondary-button>
                    </a>

                    <x-primary-button type="submit" :disabled="$availableVehicles->isEmpty()">
                        <x-icon name="save" class="w-4 h-4 mr-2" />
                        Adicionar Veículo
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
