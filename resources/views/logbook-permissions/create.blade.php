<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Permissão" subtitle="Privilégios do Diário de Bordo" hide-title-mobile icon="shield" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook-permissions.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações da Permissão">
        <form action="{{ route('logbook-permissions.store') }}" method="POST" class="space-y-6" x-data="{
            scope: '{{ old('scope', 'vehicles') }}'
        }">
            @csrf

            <!-- Campo de Pesquisa de Usuário -->
            <x-user-search
                name="user_id"
                label="Usuário *"
                :roles="['driver', 'sector_manager', 'general_manager']"
                placeholder="Digite o nome ou CPF do usuário..."
            />

            <!-- Escopo -->
            <div>
                <x-input-label for="scope" value="Tipo de Permissão *" />
                <div class="flex items-center gap-2 mt-1">
                    <x-icon name="building" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                    <x-ui.select name="scope" id="scope" x-model="scope" class="flex-1" required>
                        <option value="vehicles">Veículos Específicos</option>
                        <option value="secretariat">Secretaria Específica</option>
                        <option value="all">Todas as Secretarias</option>
                    </x-ui.select>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Defina o escopo de acesso para este usuário
                </p>
                <x-input-error :messages="$errors->get('scope')" class="mt-2" />
            </div>

            <!-- Secretaria (se scope = secretariat) - Usando o novo componente de pesquisa -->
            <div x-show="scope === 'secretariat'" x-cloak>
                <x-secretariat-search
                    name="secretariat_ids"
                    label="Secretarias *"
                    :selectedIds="old('secretariat_ids', [])"
                    placeholder="Digite o nome da secretaria..."
                />
            </div>

            <!-- Veículos (se scope = vehicles) - Usando o novo componente de pesquisa -->
            <div x-show="scope === 'vehicles'" x-cloak>
                <x-vehicle-search
                    name="vehicle_ids"
                    label="Veículos *"
                    :selectedIds="old('vehicle_ids', [])"
                    placeholder="Digite o prefixo ou placa do veículo..."
                />
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
                >{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    @checked(old('is_active', true))
                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                >
                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    Permissão ativa
                </label>
            </div>

            <!-- Botões -->
            <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-primary-button icon="save">
                    Salvar Permissão
                </x-primary-button>
                <a href="{{ route('logbook-permissions.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                    Cancelar
                </a>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
