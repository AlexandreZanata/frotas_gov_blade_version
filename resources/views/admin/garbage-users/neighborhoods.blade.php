<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Gerenciar Bairros"
            subtitle="Configure os bairros disponíveis para: {{ $garbageUser->user->name }}"
            hide-title-mobile
            icon="map-pin"
        />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('admin.garbage-users.neighborhoods.update', $garbageUser) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <x-input-label value="Bairros Disponíveis" />
                    <p class="text-sm text-gray-500 dark:text-navy-400 mb-4">
                        Selecione os bairros que este usuário poderá atender
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2">
                        @foreach($neighborhoods as $neighborhood)
                            <label class="flex items-start p-4 border border-gray-200 dark:border-navy-700 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-700/40 cursor-pointer transition-colors">
                                <input type="checkbox"
                                       name="neighborhoods[]"
                                       value="{{ $neighborhood->id }}"
                                       {{ in_array($neighborhood->id, $userNeighborhoods) ? 'checked' : '' }}
                                       class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-navy-600 dark:bg-navy-700">
                                <div class="ml-3 flex-1">
                                    <span class="text-sm font-medium text-gray-900 dark:text-navy-50">
                                        {{ $neighborhood->name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @if($neighborhoods->isEmpty())
                        <div class="text-center py-8">
                            <x-icon name="map-pin" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <p class="text-gray-500 dark:text-navy-300">Nenhum bairro cadastrado no sistema.</p>
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
                        Salvar Bairros
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
