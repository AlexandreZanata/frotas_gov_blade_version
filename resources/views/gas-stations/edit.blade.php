<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Posto" subtitle="Atualizar dados do posto" hide-title-mobile icon="fuel" />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('gas-stations.update', $gasStation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Nome do Posto <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $gasStation->name) }}"
                           required
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Endereço -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Endereço
                    </label>
                    <input type="text"
                           name="address"
                           id="address"
                           value="{{ old('address', $gasStation->address) }}"
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CNPJ -->
                <div>
                    <label for="cnpj" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        CNPJ
                    </label>
                    <input type="text"
                           name="cnpj"
                           id="cnpj"
                           value="{{ old('cnpj', $gasStation->cnpj) }}"
                           x-mask="99.999.999/9999-99"
                           placeholder="00.000.000/0000-00"
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('cnpj')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ old('status', $gasStation->status) === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $gasStation->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('gas-stations.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                    Atualizar Posto
                </button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Cadastrar Posto" subtitle="Adicionar novo posto de combustível" hide-title-mobile icon="fuel" />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('gas-stations.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Nome do Posto <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           required
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Endereço -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Endereço
                    </label>
                    <input type="text"
                           name="address"
                           id="address"
                           value="{{ old('address') }}"
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CNPJ -->
                <div>
                    <label for="cnpj" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        CNPJ
                    </label>
                    <input type="text"
                           name="cnpj"
                           id="cnpj"
                           value="{{ old('cnpj') }}"
                           x-mask="99.999.999/9999-99"
                           placeholder="00.000.000/0000-00"
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('cnpj')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('gas-stations.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                    Cadastrar Posto
                </button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>

