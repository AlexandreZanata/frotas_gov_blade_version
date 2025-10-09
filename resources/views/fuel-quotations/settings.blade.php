<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Configurações de Cotação de Combustível
            </h2>
            <a href="{{ route('fuel-quotations.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabs -->
            <div class="mb-6" x-data="{ activeTab: 'calculation' }">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'calculation'"
                                :class="activeTab === 'calculation' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            Métodos de Cálculo
                        </button>
                        <button @click="activeTab = 'discount'"
                                :class="activeTab === 'discount' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            Descontos Personalizados
                        </button>
                    </nav>
                </div>

                <!-- Tab Content: Métodos de Cálculo -->
                <div x-show="activeTab === 'calculation'" class="mt-6">
                    @foreach($fuelTypes as $fuelType)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $fuelType->name }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <!-- Botão Adicionar Método -->
                                <button @click="$dispatch('open-modal', 'add-calculation-{{ $fuelType->id }}')"
                                        class="mb-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar Método
                                </button>

                                <!-- Lista de Métodos -->
                                @if($fuelType->calculationMethods->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($fuelType->calculationMethods->sortBy('order') as $method)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3">
                                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $method->name }}</span>
                                                        <span class="px-2 py-1 text-xs rounded-full {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $method->is_active ? 'Ativo' : 'Inativo' }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                        Tipo: {{ ucfirst(str_replace('_', ' ', $method->calculation_type)) }}
                                                        @if($method->formula) | Fórmula: {{ $method->formula }} @endif
                                                    </p>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button @click="$dispatch('open-modal', 'edit-calculation-{{ $method->id }}')"
                                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('fuel-quotations.settings.calculation-methods.destroy', $method) }}" method="POST"
                                                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded transition">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Modal Editar Método -->
                                            <x-modal name="edit-calculation-{{ $method->id }}" maxWidth="2xl">
                                                <form action="{{ route('fuel-quotations.settings.calculation-methods.update', $method) }}" method="POST" class="p-6">
                                                    @csrf
                                                    @method('PUT')
                                                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Editar Método de Cálculo</h3>

                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome</label>
                                                            <input type="text" name="name" value="{{ $method->name }}" required
                                                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Cálculo</label>
                                                            <select name="calculation_type" required
                                                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                                <option value="average" {{ $method->calculation_type == 'average' ? 'selected' : '' }}>Média Simples</option>
                                                                <option value="weighted_average" {{ $method->calculation_type == 'weighted_average' ? 'selected' : '' }}>Média Ponderada</option>
                                                                <option value="custom" {{ $method->calculation_type == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fórmula (opcional)</label>
                                                            <textarea name="formula" rows="2"
                                                                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ $method->formula }}</textarea>
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <label class="flex items-center">
                                                                <input type="checkbox" name="is_active" value="1" {{ $method->is_active ? 'checked' : '' }}
                                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativo</span>
                                                            </label>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ordem:</label>
                                                                <input type="number" name="order" value="{{ $method->order }}" min="0"
                                                                       class="ml-2 w-20 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <button type="button" @click="$dispatch('close-modal', 'edit-calculation-{{ $method->id }}')"
                                                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                                            Cancelar
                                                        </button>
                                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                                            Salvar
                                                        </button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Nenhum método cadastrado.</p>
                                @endif

                                <!-- Modal Adicionar Método -->
                                <x-modal name="add-calculation-{{ $fuelType->id }}" maxWidth="2xl">
                                    <form action="{{ route('fuel-quotations.settings.calculation-methods.store') }}" method="POST" class="p-6">
                                        @csrf
                                        <input type="hidden" name="fuel_type_id" value="{{ $fuelType->id }}">
                                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Novo Método de Cálculo - {{ $fuelType->name }}</h3>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                                                <input type="text" name="name" required placeholder="Ex: Média Simples dos 3 Menores"
                                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Cálculo *</label>
                                                <select name="calculation_type" required
                                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                    <option value="average">Média Simples</option>
                                                    <option value="weighted_average">Média Ponderada</option>
                                                    <option value="custom">Personalizado</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fórmula (opcional)</label>
                                                <textarea name="formula" rows="2" placeholder="Descreva a fórmula personalizada"
                                                          class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"></textarea>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="is_active" value="1" checked
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativo</span>
                                                </label>
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ordem:</label>
                                                    <input type="number" name="order" value="0" min="0"
                                                           class="ml-2 w-20 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" @click="$dispatch('close-modal', 'add-calculation-{{ $fuelType->id }}')"
                                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                                Criar
                                            </button>
                                        </div>
                                    </form>
                                </x-modal>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tab Content: Descontos -->
                <div x-show="activeTab === 'discount'" class="mt-6">
                    @foreach($fuelTypes as $fuelType)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $fuelType->name }}
                                </h3>
                            </div>
                            <div class="p-6">
                                <!-- Botão Adicionar Desconto -->
                                <button @click="$dispatch('open-modal', 'add-discount-{{ $fuelType->id }}')"
                                        class="mb-4 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar Desconto
                                </button>

                                <!-- Lista de Descontos -->
                                @if($fuelType->discountSettings->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($fuelType->discountSettings->sortBy('order') as $discount)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3">
                                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $discount->name }}</span>
                                                        <span class="px-2 py-1 text-xs rounded-full {{ $discount->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $discount->is_active ? 'Ativo' : 'Inativo' }}
                                                        </span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                        Tipo: {{ ucfirst($discount->discount_type) }}
                                                        @if($discount->discount_type == 'percentage')
                                                            | {{ $discount->percentage }}%
                                                        @elseif($discount->discount_type == 'fixed')
                                                            | R$ {{ number_format($discount->fixed_value, 2, ',', '.') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="flex gap-2">
                                                    <button @click="$dispatch('open-modal', 'edit-discount-{{ $discount->id }}')"
                                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('fuel-quotations.settings.discount-settings.destroy', $discount) }}" method="POST"
                                                          onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded transition">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Modal Editar Desconto -->
                                            <x-modal name="edit-discount-{{ $discount->id }}" maxWidth="2xl">
                                                <form action="{{ route('fuel-quotations.settings.discount-settings.update', $discount) }}" method="POST" class="p-6">
                                                    @csrf
                                                    @method('PUT')
                                                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Editar Desconto</h3>

                                                    <div class="space-y-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome</label>
                                                            <input type="text" name="name" value="{{ $discount->name }}" required
                                                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Desconto</label>
                                                            <select name="discount_type" required
                                                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                                <option value="percentage" {{ $discount->discount_type == 'percentage' ? 'selected' : '' }}>Porcentagem</option>
                                                                <option value="fixed" {{ $discount->discount_type == 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
                                                                <option value="custom" {{ $discount->discount_type == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                                            </select>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Porcentagem (%)</label>
                                                                <input type="number" name="percentage" value="{{ $discount->percentage }}" step="0.01" min="0" max="100"
                                                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor Fixo (R$)</label>
                                                                <input type="number" name="fixed_value" value="{{ $discount->fixed_value }}" step="0.01" min="0"
                                                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <label class="flex items-center">
                                                                <input type="checkbox" name="is_active" value="1" {{ $discount->is_active ? 'checked' : '' }}
                                                                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativo</span>
                                                            </label>
                                                            <div>
                                                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ordem:</label>
                                                                <input type="number" name="order" value="{{ $discount->order }}" min="0"
                                                                       class="ml-2 w-20 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <button type="button" @click="$dispatch('close-modal', 'edit-discount-{{ $discount->id }}')"
                                                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                                            Cancelar
                                                        </button>
                                                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                                                            Salvar
                                                        </button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">Nenhum desconto cadastrado.</p>
                                @endif

                                <!-- Modal Adicionar Desconto -->
                                <x-modal name="add-discount-{{ $fuelType->id }}" maxWidth="2xl">
                                    <form action="{{ route('fuel-quotations.settings.discount-settings.store') }}" method="POST" class="p-6">
                                        @csrf
                                        <input type="hidden" name="fuel_type_id" value="{{ $fuelType->id }}">
                                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Novo Desconto - {{ $fuelType->name }}</h3>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                                                <input type="text" name="name" required placeholder="Ex: Desconto Contrato Anual"
                                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Desconto *</label>
                                                <select name="discount_type" required
                                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                    <option value="percentage">Porcentagem</option>
                                                    <option value="fixed">Valor Fixo</option>
                                                    <option value="custom">Personalizado</option>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Porcentagem (%)</label>
                                                    <input type="number" name="percentage" value="0" step="0.01" min="0" max="100"
                                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor Fixo (R$)</label>
                                                    <input type="number" name="fixed_value" value="0" step="0.01" min="0"
                                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="is_active" value="1" checked
                                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ativo</span>
                                                </label>
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ordem:</label>
                                                    <input type="number" name="order" value="0" min="0"
                                                           class="ml-2 w-20 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" @click="$dispatch('close-modal', 'add-discount-{{ $fuelType->id }}')"
                                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                                                Criar
                                            </button>
                                        </div>
                                    </form>
                                </x-modal>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

