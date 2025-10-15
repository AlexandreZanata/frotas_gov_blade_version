<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Cotação de Combustível"
                          subtitle="Cadastrar nova cotação de preços"
                          hide-title-mobile
                          icon="fuel" />
    </x-slot>

    <x-slot name="pageActions">
        <a href="{{ route('fuel-quotations.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Voltar</span>
        </a>
    </x-slot>

    <div class="space-y-6" x-data="quotationForm()">
        <!-- Informações Básicas -->
        <div class="bg-white dark:bg-navy-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-700">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informações da Cotação</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome da Cotação -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                            Nome da Cotação <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               x-model="formData.name"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Ex: Cotação Janeiro 2025">
                    </div>

                    <!-- Data da Cotação -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                            Data da Cotação <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               x-model="formData.quotation_date"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.status"
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="draft">Rascunho</option>
                            <option value="pending">Pendente</option>
                            <option value="approved">Aprovada</option>
                        </select>
                    </div>

                    <!-- Observações -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">Observações</label>
                        <textarea x-model="formData.notes"
                                  rows="3"
                                  class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Observações sobre esta cotação"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Postos e Preços -->
        <div class="bg-white dark:bg-navy-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-700">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Postos e Preços</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Cabeçalho com botão de adicionar -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Postos Participantes</h4>
                            <p class="text-sm text-gray-500 dark:text-navy-300 mt-1">Adicione os postos e seus respectivos preços</p>
                        </div>
                        <button type="button"
                                @click="addStation()"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Adicionar Posto</span>
                        </button>
                    </div>

                    <!-- Alert informativo -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800 dark:text-blue-300">
                                <p class="font-semibold mb-1">Instruções:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Preencha o preço para cada combustível disponível no posto</li>
                                    <li>Deixe em branco ou "0" para combustíveis não disponíveis</li>
                                    <li>Você pode adicionar até 2 imagens por posto (comprovantes)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Postos -->
                    <div class="space-y-4">
                        <template x-for="(station, stationIndex) in formData.stations" :key="stationIndex">
                            <div class="border border-gray-200 dark:border-navy-600 rounded-lg p-4 bg-white dark:bg-navy-800">
                                <!-- Cabeçalho do Posto -->
                                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-navy-600">
                                    <h5 class="font-semibold text-gray-900 dark:text-white">
                                        Posto <span x-text="stationIndex + 1"></span>
                                    </h5>
                                    <button type="button"
                                            @click="removeStation(stationIndex)"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="text-sm">Remover</span>
                                    </button>
                                </div>

                                <!-- Seleção do Posto -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                        Selecione o Posto <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="station.gas_station_id"
                                            :name="`stations[${stationIndex}][gas_station_id]`"
                                            required
                                            class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Selecione um posto...</option>
                                        @foreach($gasStations as $gasStation)
                                            <option value="{{ $gasStation->id }}">{{ $gasStation->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Preços por Combustível -->
                                <div class="bg-gray-50 dark:bg-navy-700 rounded-lg p-4">
                                    <h6 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Preços dos Combustíveis</h6>
                                    <div class="space-y-3">
                                        <template x-for="(price, priceIndex) in station.prices" :key="priceIndex">
                                            <div class="border border-gray-200 dark:border-navy-600 rounded-lg p-3 bg-white dark:bg-navy-800">
                                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                                                    <!-- Combustível -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">
                                                            <span x-text="price.fuel_name" class="font-medium"></span>
                                                        </label>
                                                        <input type="hidden"
                                                               :name="`stations[${stationIndex}][prices][${priceIndex}][fuel_type_id]`"
                                                               x-model="price.fuel_type_id">
                                                    </div>

                                                    <!-- Preço -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Preço (R$)</label>
                                                        <input type="number"
                                                               :name="`stations[${stationIndex}][prices][${priceIndex}][price]`"
                                                               x-model="price.price"
                                                               step="0.001"
                                                               min="0"
                                                               placeholder="0.000"
                                                               class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                                        <p class="text-xs text-gray-500 dark:text-navy-300 mt-1">
                                                            Deixe em branco se não disponível
                                                        </p>
                                                    </div>

                                                    <!-- Imagens -->
                                                    <div class="lg:col-span-2">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-1">Comprovantes</label>
                                                        <div class="flex gap-3">
                                                            <!-- Imagem 1 -->
                                                            <div class="flex-1">
                                                                <label class="cursor-pointer block">
                                                                    <input type="file"
                                                                           :name="`stations[${stationIndex}][prices][${priceIndex}][image_1]`"
                                                                           @change="handleImageUpload($event, stationIndex, priceIndex, 1)"
                                                                           accept="image/*"
                                                                           class="hidden">
                                                                    <div class="border-2 border-dashed border-gray-300 dark:border-navy-600 rounded p-2 text-center hover:border-primary-500 transition">
                                                                        <template x-if="price.image_1_preview">
                                                                            <div class="relative">
                                                                                <img :src="price.image_1_preview" class="w-full h-16 object-cover rounded">
                                                                                <button type="button"
                                                                                        @click.stop="removeImage(stationIndex, priceIndex, 1)"
                                                                                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1">
                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                                    </svg>
                                                                                </button>
                                                                            </div>
                                                                        </template>
                                                                        <template x-if="!price.image_1_preview">
                                                                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                                            </svg>
                                                                        </template>
                                                                    </div>
                                                                </label>
                                                            </div>

                                                            <!-- Imagem 2 -->
                                                            <div class="flex-1">
                                                                <label class="cursor-pointer block">
                                                                    <input type="file"
                                                                           :name="`stations[${stationIndex}][prices][${priceIndex}][image_2]`"
                                                                           @change="handleImageUpload($event, stationIndex, priceIndex, 2)"
                                                                           accept="image/*"
                                                                           class="hidden">
                                                                    <div class="border-2 border-dashed border-gray-300 dark:border-navy-600 rounded p-2 text-center hover:border-primary-500 transition">
                                                                        <template x-if="price.image_2_preview">
                                                                            <div class="relative">
                                                                                <img :src="price.image_2_preview" class="w-full h-16 object-cover rounded">
                                                                                <button type="button"
                                                                                        @click.stop="removeImage(stationIndex, priceIndex, 2)"
                                                                                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1">
                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                                    </svg>
                                                                                </button>
                                                                            </div>
                                                                        </template>
                                                                        <template x-if="!price.image_2_preview">
                                                                            <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                                            </svg>
                                                                        </template>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Estado vazio -->
                        <template x-if="formData.stations.length === 0">
                            <div class="text-center py-8 text-gray-500 dark:text-navy-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="text-sm mb-4">Nenhum posto adicionado. Clique em "Adicionar Posto" para começar.</p>
                                <button type="button"
                                        @click="addStation()"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Adicionar Primeiro Posto</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="bg-white dark:bg-navy-800 rounded-lg shadow">
            <div class="p-6">
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('fuel-quotations.index') }}"
                       class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            @click="submitForm($event)"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-6 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                        <span x-show="!submitting">Salvar Cotação</span>
                        <span x-show="submitting" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Salvando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function quotationForm() {
                return {
                    formData: {
                        name: '',
                        quotation_date: '',
                        status: 'draft',
                        notes: '',
                        stations: [],
                        pumpPrices: []
                    },
                    fuelTypes: @json($fuelTypes),
                    submitting: false,

                    init() {
                        // Inicializar com data atual
                        const today = new Date().toISOString().split('T')[0];
                        this.formData.quotation_date = today;
                    },

                    addStation() {
                        const prices = this.fuelTypes.map(fuel => ({
                            fuel_type_id: fuel.id,
                            fuel_name: fuel.name,
                            price: null,
                            image_1_preview: null,
                            image_2_preview: null
                        }));

                        this.formData.stations.push({
                            gas_station_id: '',
                            prices: prices
                        });
                    },

                    removeStation(index) {
                        if (confirm('Tem certeza que deseja remover este posto?')) {
                            this.formData.stations.splice(index, 1);
                        }
                    },

                    handleImageUpload(event, stationIndex, priceIndex, imageNumber) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.formData.stations[stationIndex].prices[priceIndex][`image_${imageNumber}_preview`] = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    },

                    removeImage(stationIndex, priceIndex, imageNumber) {
                        this.formData.stations[stationIndex].prices[priceIndex][`image_${imageNumber}_preview`] = null;
                        // Limpar o input de arquivo
                        const input = document.querySelector(`input[name="stations[${stationIndex}][prices][${priceIndex}][image_${imageNumber}]"]`);
                        if (input) input.value = '';
                    },

                    async submitForm(event) {
                        if (this.submitting) return;

                        // Validações básicas
                        if (!this.formData.name.trim()) {
                            alert('Por favor, informe o nome da cotação.');
                            return;
                        }

                        if (!this.formData.quotation_date) {
                            alert('Por favor, informe a data da cotação.');
                            return;
                        }

                        if (this.formData.stations.length === 0) {
                            alert('Adicione pelo menos um posto para continuar.');
                            return;
                        }

                        // Validar se todos os postos têm um posto selecionado
                        for (let i = 0; i < this.formData.stations.length; i++) {
                            if (!this.formData.stations[i].gas_station_id) {
                                alert(`O posto ${i + 1} não tem um posto selecionado.`);
                                return;
                            }
                        }

                        this.submitting = true;

                        try {
                            // Criar um formulário dinâmico
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('fuel-quotations.store') }}';
                            form.enctype = 'multipart/form-data';

                            // Adicionar CSRF token
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            form.appendChild(csrfToken);

                            // Adicionar dados básicos
                            const addField = (name, value) => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = name;
                                input.value = value;
                                form.appendChild(input);
                            };

                            addField('name', this.formData.name);
                            addField('quotation_date', this.formData.quotation_date);
                            addField('status', this.formData.status);
                            addField('notes', this.formData.notes);

                            // Adicionar stations
                            this.formData.stations.forEach((station, stationIndex) => {
                                addField(`stations[${stationIndex}][gas_station_id]`, station.gas_station_id);

                                station.prices.forEach((price, priceIndex) => {
                                    addField(`stations[${stationIndex}][prices][${priceIndex}][fuel_type_id]`, price.fuel_type_id);
                                    addField(`stations[${stationIndex}][prices][${priceIndex}][price]`, price.price || '');
                                });
                            });

                            // Adicionar ao documento e submeter
                            document.body.appendChild(form);
                            form.submit();

                        } catch (error) {
                            console.error('Erro:', error);
                            alert('Erro ao salvar cotação. Tente novamente.');
                            this.submitting = false;
                        }
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
