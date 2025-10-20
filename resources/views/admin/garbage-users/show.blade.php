<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Detalhes do Usuário"
            subtitle="Informações completas do usuário de lixo"
            hide-title-mobile
            icon="user"
        />
    </x-slot>

    <div class="space-y-6">
        <!-- Informações do Usuário -->
        <x-ui.card title="Informações do Usuário">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label value="Nome" />
                    <p class="mt-1 text-base text-gray-900 dark:text-navy-50">{{ $garbageUser->user->name }}</p>
                </div>
                <div>
                    <x-input-label value="Email" />
                    <p class="mt-1 text-base text-gray-900 dark:text-navy-50">{{ $garbageUser->user->email }}</p>
                </div>
                <div>
                    <x-input-label value="CPF" />
                    <p class="mt-1 text-base text-gray-900 dark:text-navy-50">{{ $garbageUser->user->cpf ?? 'Não informado' }}</p>
                </div>
                <div>
                    <x-input-label value="Status" />
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            Ativo
                        </span>
                    </p>
                </div>
            </div>
        </x-ui.card>

        <!-- Veículos Associados -->
        <x-ui.card title="Veículos Associados">
            @if($garbageUser->vehicles->count() > 0)
                <div class="space-y-3">
                    @foreach($garbageUser->vehicles as $vehicle)
                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-navy-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <x-icon name="truck" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-navy-50">
                                        {{ $vehicle->vehicle->prefix->name ?? 'N/A' }} - {{ $vehicle->vehicle->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-navy-300">
                                        Placa: {{ $vehicle->vehicle->plate }} •
                                        Categoria: {{ $vehicle->vehicle->category->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <x-icon name="truck" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-500 dark:text-navy-300">Nenhum veículo associado a este usuário.</p>
                </div>
            @endif
        </x-ui.card>

        <!-- Bairros Associados -->
        <x-ui.card title="Bairros Associados">
            @if($garbageUser->neighborhoods->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($garbageUser->neighborhoods as $neighborhood)
                        <div class="flex items-center p-3 border border-gray-200 dark:border-navy-700 rounded-lg">
                            <x-icon name="map-pin" class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
                            <span class="text-sm font-medium text-gray-900 dark:text-navy-50">{{ $neighborhood->name }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <x-icon name="map-pin" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-500 dark:text-navy-300">Nenhum bairro associado a este usuário.</p>
                </div>
            @endif
        </x-ui.card>

        <!-- Ações -->
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.garbage-users.index') }}">
                <x-secondary-button>
                    <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                    Voltar para Lista
                </x-secondary-button>
            </a>

            <div class="flex space-x-3">
                <a href="{{ route('admin.garbage-users.vehicles.edit', $garbageUser) }}">
                    <x-primary-button variant="outline">
                        <x-icon name="truck" class="w-4 h-4 mr-2" />
                        Gerenciar Veículos
                    </x-primary-button>
                </a>
                <a href="{{ route('admin.garbage-users.neighborhoods.edit', $garbageUser) }}">
                    <x-primary-button variant="outline">
                        <x-icon name="map-pin" class="w-4 h-4 mr-2" />
                        Gerenciar Bairros
                    </x-primary-button>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
