<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Permissão" subtitle="Privilégios do Diário de Bordo" hide-title-mobile icon="shield" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook-permissions.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações da Permissão">
        <form action="{{ route('logbook-permissions.update', $logbookPermission) }}" method="POST" class="space-y-6" x-data="{
            scope: '{{ old('scope', $logbookPermission->scope) }}'
        }">
            @csrf
            @method('PUT')

            <!-- Campo de Pesquisa de Usuário -->
            <x-user-search
                name="user_id"
                label="Usuário *"
                :roles="['driver', 'sector_manager']"
                placeholder="Digite o nome ou CPF do usuário..."
                :selectedId="old('user_id', $logbookPermission->user_id)"
                :selectedName="old('user_name', $logbookPermission->user->name ?? '')"
            />

            <!-- Escopo -->
            <div>
                <x-input-label for="scope" value="Tipo de Permissão *" />
                <x-ui.select name="scope" id="scope" x-model="scope" class="mt-1" required>
                    <option value="vehicles">Veículos Específicos</option>
                    <option value="secretariat">Secretaria Específica</option>
                    <option value="all">Todas as Secretarias</option>
                </x-ui.select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Defina o escopo de acesso para este usuário
                </p>
                <x-input-error :messages="$errors->get('scope')" class="mt-2" />
            </div>

            <!-- Secretaria (se scope = secretariat) -->
            <div x-show="scope === 'secretariat'" x-cloak>
                <x-input-label for="secretariat_id" value="Secretaria *" />
                <x-ui.select name="secretariat_id" id="secretariat_id" class="mt-1">
                    <option value="">Selecione uma secretaria...</option>
                    @foreach($secretariats as $secretariat)
                        <option value="{{ $secretariat->id }}" @selected(old('secretariat_id', $logbookPermission->secretariat_id) == $secretariat->id)>
                            {{ $secretariat->name }}
                        </option>
                    @endforeach
                </x-ui.select>
                <x-input-error :messages="$errors->get('secretariat_id')" class="mt-2" />
            </div>

            <!-- Veículos (se scope = vehicles) -->
            <div x-show="scope === 'vehicles'" x-cloak>
                <x-input-label for="vehicle_ids" value="Veículos *" />
                <div class="mt-2 max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md p-4 bg-gray-50 dark:bg-gray-900">
                    @foreach($vehicles as $vehicle)
                        <label class="flex items-center py-2 hover:bg-gray-100 dark:hover:bg-gray-800 px-2 rounded cursor-pointer">
                            <input
                                type="checkbox"
                                name="vehicle_ids[]"
                                value="{{ $vehicle->id }}"
                                @checked(in_array($vehicle->id, old('vehicle_ids', $logbookPermission->vehicles->pluck('id')->toArray())))
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                            />
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                <span class="font-medium">{{ $vehicle->prefix->name ?? 'N/A' }}</span> - {{ $vehicle->name }}
                                <span class="text-gray-500">({{ $vehicle->plate }})</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Selecione os veículos que o usuário poderá utilizar no diário de bordo
                </p>
                <x-input-error :messages="$errors->get('vehicle_ids')" class="mt-2" />
            </div>

            <!-- Descrição -->
            <div>
                <x-input-label for="description" value="Observações" />
                <textarea
                    name="description"
                    id="description"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                    placeholder="Adicione observações sobre esta permissão (opcional)"
                >{{ old('description', $logbookPermission->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    @checked(old('is_active', $logbookPermission->is_active))
                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                >
                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    Permissão ativa
                </label>
            </div>

            <!-- Botões -->
            <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-primary-button icon="save">
                    Atualizar Permissão
                </x-primary-button>
                <a href="{{ route('logbook-permissions.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                    Cancelar
                </a>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
