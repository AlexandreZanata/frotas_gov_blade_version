<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Configurações de Cotação de Combustível"
            subtitle="Gestão de métodos de cálculo e descontos personalizados"
            hide-title-mobile
            icon="cog"
        />
    </x-slot>

    <x-slot name="pageActions">
        <x-ui.action-icon
            :href="route('fuel-quotations.index')"
            icon="arrow-left"
            title="Voltar"
            variant="neutral"
        />
    </x-slot>

    <x-ui.card>
        <!-- Mensagem de Sucesso -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-green-800 dark:text-green-300 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabs -->
        <div x-data="{
            activeTab: new URLSearchParams(window.location.search).get('tab') || 'calculation',

            setActiveTab(tab) {
                this.activeTab = tab;
                // Atualizar URL sem recarregar a página
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            }
        }">
            <div class="border-b border-gray-200 dark:border-navy-700">
                <nav class="-mb-px flex space-x-8">
                    <button @click="setActiveTab('calculation')"
                            :class="activeTab === 'calculation'
                                ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Métodos de Cálculo
                    </button>
                    <button @click="setActiveTab('discount')"
                            :class="activeTab === 'discount'
                                ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Descontos Personalizados
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Métodos de Cálculo -->
            <div x-show="activeTab === 'calculation'" class="mt-6 space-y-6">
                @foreach($fuelTypes as $fuelType)
                    <div class="border border-gray-200 dark:border-navy-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-700 bg-gray-50 dark:bg-navy-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-icon name="fuel" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                {{ $fuelType->name ?? 'Tipo de Combustível' }}
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-medium text-gray-700 dark:text-navy-200">
                                    Métodos de Cálculo Configurados
                                </h4>
                                <button type="button"
                                        @click="$dispatch('open-modal', 'add-calculation-{{ $fuelType->id }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                                    <x-icon name="plus" class="w-4 h-4" />
                                    <span>Adicionar Método</span>
                                </button>
                            </div>

                            @if($fuelType->calculationMethods && $fuelType->calculationMethods->count() > 0)
                                <div class="space-y-3">
                                    @foreach($fuelType->calculationMethods->sortBy('order') as $method)
                                        <div class="flex items-center justify-between p-4 bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-600 rounded-lg">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $method->name ?? 'Nome não definido' }}</span>
                                                    <x-ui.status-badge :status="($method->is_active ?? false) ? 'active' : 'inactive'" />
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-navy-300 space-y-1">
                                                    <p><strong>Tipo:</strong> {{ isset($method->calculation_type) ? ucfirst(str_replace('_', ' ', $method->calculation_type)) : 'Não definido' }}</p>
                                                    @if(!empty($method->formula))
                                                        <p><strong>Fórmula:</strong> {{ $method->formula }}</p>
                                                    @endif
                                                    <p><strong>Ordem:</strong> {{ $method->order ?? 0 }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <!-- Botão Editar -->
                                                <button type="button"
                                                        @click="() => {
                                                            $dispatch('open-modal', 'edit-calculation-{{ $method->id }}');
                                                            event.preventDefault();
                                                        }"
                                                        class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                                                        title="Editar método">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>

                                                <!-- Botão Excluir -->
                                                <form action="{{ route('fuel-quotations.settings.calculation-methods.destroy', $method) }}"
                                                      method="POST"
                                                      class="inline-block"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este método de cálculo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
                                                            title="Excluir método">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Modal Editar Método -->
                                        <x-modal name="edit-calculation-{{ $method->id }}" maxWidth="2xl">
                                            <form action="{{ route('fuel-quotations.settings.calculation-methods.update', $method) }}" method="POST" class="p-6"
                                                  @submit="(e) => {
                                                      // Manter a aba ativa após submit
                                                      const form = e.target;
                                                      const hiddenInput = document.createElement('input');
                                                      hiddenInput.type = 'hidden';
                                                      hiddenInput.name = 'active_tab';
                                                      hiddenInput.value = activeTab;
                                                      form.appendChild(hiddenInput);
                                                  }">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="active_tab" x-model="activeTab">

                                                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                                                    <x-icon name="edit" class="w-5 h-5 text-primary-600" />
                                                    Editar Método de Cálculo
                                                </h3>

                                                <div class="space-y-4">
                                                    <div>
                                                        <x-input-label for="name" value="Nome" />
                                                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                                                      :value="old('name', $method->name ?? '')" required />
                                                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                                    </div>

                                                    <div>
                                                        <x-input-label for="calculation_type" value="Tipo de Cálculo" />
                                                        <x-ui.select name="calculation_type" id="calculation_type" class="mt-1" required>
                                                            <option value="">Selecione...</option>
                                                            <option value="average" {{ ($method->calculation_type ?? '') == 'average' ? 'selected' : '' }}>Média Simples</option>
                                                            <option value="weighted_average" {{ ($method->calculation_type ?? '') == 'weighted_average' ? 'selected' : '' }}>Média Ponderada</option>
                                                            <option value="custom" {{ ($method->calculation_type ?? '') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                                        </x-ui.select>
                                                        <x-input-error :messages="$errors->get('calculation_type')" class="mt-1" />
                                                    </div>

                                                    <div>
                                                        <x-input-label for="formula" value="Fórmula (opcional)" />
                                                        <textarea id="formula" name="formula" rows="3"
                                                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                                                                  placeholder="Descreva a fórmula personalizada">{{ old('formula', $method->formula ?? '') }}</textarea>
                                                        <x-input-error :messages="$errors->get('formula')" class="mt-1" />
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                                                   {{ old('is_active', $method->is_active ?? false) ? 'checked' : '' }}
                                                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                            <x-input-label for="is_active" value="Ativo" class="ml-2" />
                                                        </div>

                                                        <div>
                                                            <x-input-label for="order" value="Ordem" />
                                                            <x-text-input id="order" name="order" type="number"
                                                                          class="mt-1 block w-full"
                                                                          :value="old('order', $method->order ?? 0)"
                                                                          min="0" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-6 flex justify-end gap-3">
                                                    <button type="button"
                                                            @click="$dispatch('close-modal', 'edit-calculation-{{ $method->id }}')"
                                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition">
                                                        <x-icon name="save" class="w-4 h-4 mr-2" />
                                                        Salvar
                                                    </button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500 dark:text-navy-300">
                                    <x-icon name="calculator" class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                                    <p class="text-sm">Nenhum método de cálculo cadastrado.</p>
                                </div>
                            @endif

                            <!-- Modal Adicionar Método -->
                            <x-modal name="add-calculation-{{ $fuelType->id }}" maxWidth="2xl">
                                <form action="{{ route('fuel-quotations.settings.calculation-methods.store') }}" method="POST" class="p-6"
                                      @submit="(e) => {
                                          // Manter a aba ativa após submit
                                          const form = e.target;
                                          const hiddenInput = document.createElement('input');
                                          hiddenInput.type = 'hidden';
                                          hiddenInput.name = 'active_tab';
                                          hiddenInput.value = activeTab;
                                          form.appendChild(hiddenInput);
                                      }">
                                    @csrf
                                    <input type="hidden" name="fuel_type_id" value="{{ $fuelType->id }}">
                                    <input type="hidden" name="active_tab" x-model="activeTab">

                                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                                        <x-icon name="plus" class="w-5 h-5 text-primary-600" />
                                        Novo Método de Cálculo - {{ $fuelType->name ?? 'Tipo de Combustível' }}
                                    </h3>

                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="name" value="Nome *" />
                                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                                          :value="old('name')"
                                                          placeholder="Ex: Média Simples dos 3 Menores" required />
                                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                        </div>

                                        <div>
                                            <x-input-label for="calculation_type" value="Tipo de Cálculo *" />
                                            <x-ui.select name="calculation_type" id="calculation_type" class="mt-1" required>
                                                <option value="">Selecione...</option>
                                                <option value="average" {{ old('calculation_type') == 'average' ? 'selected' : '' }}>Média Simples</option>
                                                <option value="weighted_average" {{ old('calculation_type') == 'weighted_average' ? 'selected' : '' }}>Média Ponderada</option>
                                                <option value="custom" {{ old('calculation_type') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                            </x-ui.select>
                                            <x-input-error :messages="$errors->get('calculation_type')" class="mt-1" />
                                        </div>

                                        <div>
                                            <x-input-label for="formula" value="Fórmula (opcional)" />
                                            <textarea id="formula" name="formula" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-primary-500 focus:ring-primary-500"
                                                      placeholder="Descreva a fórmula personalizada">{{ old('formula') }}</textarea>
                                            <x-input-error :messages="$errors->get('formula')" class="mt-1" />
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                                       {{ old('is_active', true) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                <x-input-label for="is_active" value="Ativo" class="ml-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="order" value="Ordem" />
                                                <x-text-input id="order" name="order" type="number"
                                                              class="mt-1 block w-full"
                                                              :value="old('order', 0)"
                                                              min="0" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button"
                                                @click="$dispatch('close-modal', 'add-calculation-{{ $fuelType->id }}')"
                                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition">
                                            <x-icon name="save" class="w-4 h-4 mr-2" />
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
            <div x-show="activeTab === 'discount'" class="mt-6 space-y-6">
                @foreach($fuelTypes as $fuelType)
                    <div class="border border-gray-200 dark:border-navy-700 rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-700 bg-gray-50 dark:bg-navy-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-icon name="fuel" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                {{ $fuelType->name ?? 'Tipo de Combustível' }}
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-medium text-gray-700 dark:text-navy-200">
                                    Descontos Personalizados
                                </h4>
                                <button type="button"
                                        @click="$dispatch('open-modal', 'add-discount-{{ $fuelType->id }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium shadow transition">
                                    <x-icon name="plus" class="w-4 h-4" />
                                    <span>Adicionar Desconto</span>
                                </button>
                            </div>

                            @if($fuelType->discountSettings && $fuelType->discountSettings->count() > 0)
                                <div class="space-y-3">
                                    @foreach($fuelType->discountSettings->sortBy('order') as $discount)
                                        <div class="flex items-center justify-between p-4 bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-600 rounded-lg">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $discount->name ?? 'Nome não definido' }}</span>
                                                    <x-ui.status-badge :status="($discount->is_active ?? false) ? 'active' : 'inactive'" />
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-navy-300 space-y-1">
                                                    <p><strong>Tipo:</strong> {{ isset($discount->discount_type) ? ucfirst($discount->discount_type) : 'Não definido' }}</p>
                                                    @if(($discount->discount_type ?? '') == 'percentage')
                                                        <p><strong>Porcentagem:</strong> {{ $discount->percentage ?? 0 }}%</p>
                                                    @elseif(($discount->discount_type ?? '') == 'fixed')
                                                        <p><strong>Valor Fixo:</strong> R$ {{ number_format($discount->fixed_value ?? 0, 2, ',', '.') }}</p>
                                                    @endif
                                                    <p><strong>Ordem:</strong> {{ $discount->order ?? 0 }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <!-- Botão Editar -->
                                                <button type="button"
                                                        @click="() => {
                                                            $dispatch('open-modal', 'edit-discount-{{ $discount->id }}');
                                                            event.preventDefault();
                                                        }"
                                                        class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                                                        title="Editar desconto">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>

                                                <!-- Botão Excluir -->
                                                <form action="{{ route('fuel-quotations.settings.discount-settings.destroy', $discount) }}"
                                                      method="POST"
                                                      class="inline-block"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este desconto?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
                                                            title="Excluir desconto">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Modal Editar Desconto -->
                                        <x-modal name="edit-discount-{{ $discount->id }}" maxWidth="2xl">
                                            <form action="{{ route('fuel-quotations.settings.discount-settings.update', $discount) }}" method="POST" class="p-6"
                                                  @submit="(e) => {
                                                      // Manter a aba ativa após submit
                                                      const form = e.target;
                                                      const hiddenInput = document.createElement('input');
                                                      hiddenInput.type = 'hidden';
                                                      hiddenInput.name = 'active_tab';
                                                      hiddenInput.value = activeTab;
                                                      form.appendChild(hiddenInput);
                                                  }">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="active_tab" x-model="activeTab">

                                                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                                                    <x-icon name="edit" class="w-5 h-5 text-primary-600" />
                                                    Editar Desconto
                                                </h3>

                                                <div class="space-y-4">
                                                    <div>
                                                        <x-input-label for="name" value="Nome" />
                                                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                                                      :value="old('name', $discount->name ?? '')" required />
                                                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                                    </div>

                                                    <div>
                                                        <x-input-label for="discount_type" value="Tipo de Desconto" />
                                                        <x-ui.select name="discount_type" id="discount_type" class="mt-1" required>
                                                            <option value="">Selecione...</option>
                                                            <option value="percentage" {{ ($discount->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Porcentagem</option>
                                                            <option value="fixed" {{ ($discount->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
                                                            <option value="custom" {{ ($discount->discount_type ?? '') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                                        </x-ui.select>
                                                        <x-input-error :messages="$errors->get('discount_type')" class="mt-1" />
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <x-input-label for="percentage" value="Porcentagem (%)" />
                                                            <x-text-input id="percentage" name="percentage" type="number"
                                                                          step="0.01" min="0" max="100"
                                                                          class="mt-1 block w-full"
                                                                          :value="old('percentage', $discount->percentage ?? 0)" />
                                                        </div>

                                                        <div>
                                                            <x-input-label for="fixed_value" value="Valor Fixo (R$)" />
                                                            <x-text-input id="fixed_value" name="fixed_value" type="number"
                                                                          step="0.01" min="0"
                                                                          class="mt-1 block w-full"
                                                                          :value="old('fixed_value', $discount->fixed_value ?? 0)" />
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                                                   {{ old('is_active', $discount->is_active ?? false) ? 'checked' : '' }}
                                                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                            <x-input-label for="is_active" value="Ativo" class="ml-2" />
                                                        </div>

                                                        <div>
                                                            <x-input-label for="order" value="Ordem" />
                                                            <x-text-input id="order" name="order" type="number"
                                                                          class="mt-1 block w-full"
                                                                          :value="old('order', $discount->order ?? 0)"
                                                                          min="0" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-6 flex justify-end gap-3">
                                                    <button type="button"
                                                            @click="$dispatch('close-modal', 'edit-discount-{{ $discount->id }}')"
                                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition">
                                                        <x-icon name="save" class="w-4 h-4 mr-2" />
                                                        Salvar
                                                    </button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500 dark:text-navy-300">
                                    <x-icon name="tag" class="w-12 h-12 mx-auto mb-3 text-gray-400" />
                                    <p class="text-sm">Nenhum desconto cadastrado.</p>
                                </div>
                            @endif

                            <!-- Modal Adicionar Desconto -->
                            <x-modal name="add-discount-{{ $fuelType->id }}" maxWidth="2xl">
                                <form action="{{ route('fuel-quotations.settings.discount-settings.store') }}" method="POST" class="p-6"
                                      @submit="(e) => {
                                          // Manter a aba ativa após submit
                                          const form = e.target;
                                          const hiddenInput = document.createElement('input');
                                          hiddenInput.type = 'hidden';
                                          hiddenInput.name = 'active_tab';
                                          hiddenInput.value = activeTab;
                                          form.appendChild(hiddenInput);
                                      }">
                                    @csrf
                                    <input type="hidden" name="fuel_type_id" value="{{ $fuelType->id }}">
                                    <input type="hidden" name="active_tab" x-model="activeTab">

                                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                                        <x-icon name="plus" class="w-5 h-5 text-primary-600" />
                                        Novo Desconto - {{ $fuelType->name ?? 'Tipo de Combustível' }}
                                    </h3>

                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="name" value="Nome *" />
                                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                                          :value="old('name')"
                                                          placeholder="Ex: Desconto Contrato Anual" required />
                                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                        </div>

                                        <div>
                                            <x-input-label for="discount_type" value="Tipo de Desconto *" />
                                            <x-ui.select name="discount_type" id="discount_type" class="mt-1" required>
                                                <option value="">Selecione...</option>
                                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Porcentagem</option>
                                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
                                                <option value="custom" {{ old('discount_type') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                            </x-ui.select>
                                            <x-input-error :messages="$errors->get('discount_type')" class="mt-1" />
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="percentage" value="Porcentagem (%)" />
                                                <x-text-input id="percentage" name="percentage" type="number"
                                                              step="0.01" min="0" max="100"
                                                              class="mt-1 block w-full"
                                                              :value="old('percentage', 0)" />
                                            </div>

                                            <div>
                                                <x-input-label for="fixed_value" value="Valor Fixo (R$)" />
                                                <x-text-input id="fixed_value" name="fixed_value" type="number"
                                                              step="0.01" min="0"
                                                              class="mt-1 block w-full"
                                                              :value="old('fixed_value', 0)" />
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                                       {{ old('is_active', true) ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                <x-input-label for="is_active" value="Ativo" class="ml-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="order" value="Ordem" />
                                                <x-text-input id="order" name="order" type="number"
                                                              class="mt-1 block w-full"
                                                              :value="old('order', 0)"
                                                              min="0" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button"
                                                @click="$dispatch('close-modal', 'add-discount-{{ $fuelType->id }}')"
                                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition">
                                            <x-icon name="save" class="w-4 h-4 mr-2" />
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
    </x-ui.card>
</x-app-layout>
