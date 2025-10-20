<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Gerenciar Veículos"
            subtitle="Configure os veículos disponíveis para: {{ $garbageUser->user->name }}"
            hide-title-mobile
            icon="truck"
        />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('admin.garbage-users.vehicles.update', $garbageUser) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <x-input-label value="Veículos Disponíveis" />
                    <p class="text-sm text-gray-500 dark:text-navy-400 mb-4">
                        Selecione os veículos que este usuário poderá utilizar
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2">
                        @foreach($vehicles as $vehicle)
                            <label class="flex items-start p-4 border border-gray-200 dark:border-navy-700 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700/40 cursor-pointer transition-colors">
                                <input type="checkbox"
                                       name="vehicles[]"
                                       value="{{ $vehicle->id }}"
                                       {{ in_array($vehicle->id, $userVehicles) ? 'checked' : '' }}
                                       class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-navy-600 dark:bg-navy-700">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-navy-50">
                                            {{ $vehicle->vehicle->prefix->name ?? 'N/A' }} - {{ $vehicle->vehicle->name }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-navy-300">
                                        Placa: {{ $vehicle->vehicle->plate }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-navy-300">
                                        Categoria: {{ $vehicle->vehicle->category->name ?? 'N/A' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @if($vehicles->isEmpty())
                        <div class="text-center py-8">
                            <x-icon name="truck" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <p class="text-gray-500 dark:text-navy-300">Nenhum veículo cadastrado no sistema.</p>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('admin.garbage-users.index') }}">
                        <x-secondary-button type="button">
                            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                            Voltar
                        </x-secondary-button>
                    </a>

                    <x-primary-button type="submit">
                        <x-icon name="save" class="w-4 h-4 mr-2" />
                        Salvar Veículos
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
