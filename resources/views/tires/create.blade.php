<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Cadastrar Novo Pneu"
            subtitle="Adicionar pneu ao estoque do sistema"
            hide-title-mobile
            icon="plus-circle"
        />
    </x-slot>

    <x-slot name="pageActions">
        <a href="{{ route('tires.stock') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white text-sm font-medium shadow transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Voltar</span>
        </a>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal - Formulário -->
            <div class="lg:col-span-2">
                <!-- Informações -->
                <x-ui.alert-card title="Cadastro de Pneu" variant="info" icon="info" class="mb-6">
                    <p>Preencha os dados do pneu para adicionar ao estoque. Após o cadastro, você poderá instalar o pneu em um veículo.</p>
                </x-ui.alert-card>

                <!-- Formulário -->
                <x-ui.card title="Dados do Pneu" subtitle="Campos obrigatórios marcados com *">
                    <form action="{{ route('tires.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Tipo de Pneu -->
                        <div>
                            <x-input-label for="inventory_item_id" value="Tipo de Pneu *" />
                            <select name="inventory_item_id" id="inventory_item_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Selecione o tipo...</option>
                                @foreach($inventoryItems as $item)
                                    <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} - {{ $item->description }}
                                    </option>
                                @endforeach
                            </select>
                            @error('inventory_item_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Marca -->
                            <div>
                                <x-input-label for="brand" value="Marca *" />
                                <input type="text" name="brand" id="brand" required value="{{ old('brand') }}"
                                       placeholder="Ex: Michelin, Pirelli, Goodyear"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                @error('brand')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Modelo -->
                            <div>
                                <x-input-label for="model" value="Modelo *" />
                                <input type="text" name="model" id="model" required value="{{ old('model') }}"
                                       placeholder="Ex: 175/70 R13"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                @error('model')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Número de Série -->
                            <div>
                                <x-input-label for="serial_number" value="Número de Série / Fogo *" />
                                <input type="text" name="serial_number" id="serial_number" required value="{{ old('serial_number') }}"
                                       placeholder="Número único de identificação"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">Deve ser único no sistema</p>
                                @error('serial_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Código DOT -->
                            <div>
                                <x-input-label for="dot_number" value="Código DOT" />
                                <input type="text" name="dot_number" id="dot_number" value="{{ old('dot_number') }}"
                                       placeholder="Ex: DOT 3423"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">Código de fabricação (opcional)</p>
                                @error('dot_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Data de Compra -->
                            <div>
                                <x-input-label for="purchase_date" value="Data de Compra *" />
                                <input type="date" name="purchase_date" id="purchase_date" required value="{{ old('purchase_date', date('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                @error('purchase_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Preço de Compra -->
                            <div>
                                <x-input-label for="purchase_price" value="Preço de Compra (R$)" />
                                <input type="number" name="purchase_price" id="purchase_price" step="0.01" min="0" value="{{ old('purchase_price') }}"
                                       placeholder="0.00"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">Valor pago pelo pneu (opcional)</p>
                                @error('purchase_price')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Vida Útil Estimada -->
                        <div>
                            <x-input-label for="lifespan_km" value="Vida Útil Estimada (KM) *" />
                            <input type="number" name="lifespan_km" id="lifespan_km" required min="1000" step="1000" value="{{ old('lifespan_km', 40000) }}"
                                   placeholder="Ex: 40000"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">
                                Quilometragem estimada que o pneu pode percorrer (geralmente entre 30.000 e 60.000 km)
                            </p>
                            @error('lifespan_km')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observações -->
                        <div>
                            <x-input-label for="notes" value="Observações" />
                            <textarea name="notes" id="notes" rows="4"
                                      placeholder="Informações adicionais sobre o pneu (garantia, fornecedor, etc)"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                            <a href="{{ route('tires.stock') }}"
                               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white rounded-md font-medium transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium shadow transition">
                                <x-icon name="check" class="w-4 h-4" />
                                Cadastrar Pneu
                            </button>
                        </div>
                    </form>
                </x-ui.card>
            </div>

            <!-- Coluna Lateral - Dicas e Informações -->
            <div class="lg:col-span-1">
                <!-- Dicas -->
                <x-ui.alert-card title="Dicas Importantes" variant="info" icon="lightbulb" class="mb-6">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li>O número de série deve ser único para cada pneu no sistema</li>
                        <li>A vida útil estimada varia conforme o tipo, marca e condições de uso</li>
                        <li>Pneus novos são automaticamente marcados como "Novo" e "Em Estoque"</li>
                        <li>Você poderá instalar o pneu em um veículo através da seção "Veículos"</li>
                        <li>O sistema calculará automaticamente a condição baseada no uso</li>
                    </ul>
                </x-ui.alert-card>

                <!-- Informações sobre Código DOT -->
                <x-ui.card title="Sobre o Código DOT" subtitle="Informação adicional">
                    <div class="text-sm text-gray-600 dark:text-navy-400 space-y-2">
                        <p>O código DOT é um identificador de fabricação do pneu que contém:</p>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li>Semana de fabricação</li>
                            <li>Ano de fabricação</li>
                            <li>Código da fábrica</li>
                        </ul>
                        <p class="text-xs mt-2">Exemplo: DOT 3423 significa que o pneu foi fabricado na 34ª semana de 2023</p>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
