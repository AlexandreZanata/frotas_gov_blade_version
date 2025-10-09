<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Nova Cotação de Combustível
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

    <div class="py-6" x-data="quotationForm()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submitForm" enctype="multipart/form-data">
                @csrf

                <!-- Informações Básicas -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informações da Cotação</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Cotação *</label>
                            <input type="text" x-model="formData.name" required
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                   placeholder="Ex: Cotação Janeiro 2025">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da Cotação *</label>
                            <input type="date" x-model="formData.quotation_date" required
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observações</label>
                            <textarea x-model="formData.notes" rows="3"
                                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                      placeholder="Observações sobre esta cotação"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Postos e Preços -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Postos e Preços</h3>
                        <button type="button" @click="addStation()"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Posto
                        </button>
                    </div>

                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="space-y-6">
                        <template x-for="(station, stationIndex) in formData.stations" :key="stationIndex">
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        Posto <span x-text="stationIndex + 1"></span>
                                    </h4>
                                    <button type="button" @click="removeStation(stationIndex)"
                                            class="text-red-600 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Seleção do Posto -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selecione o Posto *</label>
                                    <select x-model="station.gas_station_id" required
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                        <option value="">Selecione...</option>
                                        @foreach($gasStations as $gasStation)
                                            <option value="{{ $gasStation->id }}">{{ $gasStation->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Preços por Combustível -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Preços dos Combustíveis</h5>
                                    <div class="space-y-4">
                                        <template x-for="(price, priceIndex) in station.prices" :key="priceIndex">
                                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <!-- Combustível -->
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            <span x-text="price.fuel_name"></span>
                                                        </label>
                                                        <input type="hidden" :name="`stations[${stationIndex}][prices][${priceIndex}][fuel_type_id]`" x-model="price.fuel_type_id">
                                                    </div>

                                                    <!-- Preço -->
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Preço (R$)</label>
                                                        <input type="number"
                                                               :name="`stations[${stationIndex}][prices][${priceIndex}][price]`"
                                                               x-model="price.price"
                                                               step="0.001"
                                                               min="0"
                                                               placeholder="0.000"
                                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 text-sm">
                                                        <p class="text-xs text-gray-500 mt-1">Deixe em branco ou "0" para não considerar</p>
                                                    </div>

                                                    <!-- Imagens -->
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Imagens</label>
                                                        <div class="flex gap-2">
                                                            <!-- Imagem 1 -->
                                                            <div class="flex-1">
                                                                <label class="cursor-pointer">
                                                                    <input type="file"
                                                                           :name="`stations[${stationIndex}][prices][${priceIndex}][image_1]`"
                                                                           @change="handleImageUpload($event, stationIndex, priceIndex, 1)"
                                                                           accept="image/*"
                                                                           class="hidden">
                                                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded p-2 text-center hover:border-blue-500 transition">
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
                                                                <label class="cursor-pointer">
                                                                    <input type="file"
                                                                           :name="`stations[${stationIndex}][prices][${priceIndex}][image_2]`"
                                                                           @change="handleImageUpload($event, stationIndex, priceIndex, 2)"
                                                                           accept="image/*"
                                                                           class="hidden">
                                                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded p-2 text-center hover:border-blue-500 transition">
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

                        <template x-if="formData.stations.length === 0">
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p>Nenhum posto adicionado. Clique em "Adicionar Posto" para começar.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Preços de Bomba (Opcional) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Preços de Bomba (Opcional)</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registre os preços reais praticados nos postos de combustível</p>
                        </div>
                        <button type="button" @click="addPumpPrice()"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Preço de Bomba
                        </button>
                    </div>

                    <!-- Lista de Preços de Bomba -->
                    <div class="space-y-4">
                        <template x-for="(pumpPrice, pumpIndex) in formData.pumpPrices" :key="pumpIndex">
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="font-medium text-gray-900 dark:text-gray-100">Preço <span x-text="pumpIndex + 1"></span></h5>
                                    <button type="button" @click="removePumpPrice(pumpIndex)"
                                            class="text-red-600 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Posto -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posto *</label>
                                        <select :name="`pump_prices[${pumpIndex}][gas_station_id]`" x-model="pumpPrice.gas_station_id" required
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="">Selecione...</option>
                                            @foreach($gasStations as $gasStation)
                                                <option value="{{ $gasStation->id }}">{{ $gasStation->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Combustível -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Combustível *</label>
                                        <select :name="`pump_prices[${pumpIndex}][fuel_type_id]`" x-model="pumpPrice.fuel_type_id" required
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="">Selecione...</option>
                                            @foreach($fuelTypes as $fuelType)
                                                <option value="{{ $fuelType->id }}">{{ $fuelType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Preço -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço (R$) *</label>
                                        <input type="number"
                                               :name="`pump_prices[${pumpIndex}][pump_price]`"
                                               x-model="pumpPrice.pump_price"
                                               step="0.001"
                                               min="0"
                                               required
                                               placeholder="0.000"
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    </div>

                                    <!-- Comprovante -->
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Comprovante (Opcional)</label>
                                        <label class="cursor-pointer">
                                            <input type="file"
                                                   :name="`pump_prices[${pumpIndex}][evidence]`"
                                                   @change="handlePumpImageUpload($event, pumpIndex)"
                                                   accept="image/*"
                                                   class="hidden">
                                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-green-500 transition">
                                                <template x-if="pumpPrice.evidence_preview">
                                                    <div class="relative inline-block">
                                                        <img :src="pumpPrice.evidence_preview" class="h-32 object-cover rounded">
                                                        <button type="button"
                                                                @click.stop="removePumpImage(pumpIndex)"
                                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                                <template x-if="!pumpPrice.evidence_preview">
                                                    <div class="py-4">
                                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">Clique para adicionar comprovante</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="formData.pumpPrices.length === 0">
                            <div class="text-center py-6 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="text-sm">Nenhum preço de bomba adicionado. Esta seção é opcional.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('fuel-quotations.index') }}"
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                        Cancelar
                    </a>
                    <button type="submit"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                        <span x-show="!submitting">Salvar Cotação</span>
                        <span x-show="submitting">Salvando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function quotationForm() {
            return {
                formData: {
                    name: '',
                    quotation_date: '',
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

                addPumpPrice() {
                    this.formData.pumpPrices.push({
                        gas_station_id: '',
                        fuel_type_id: '',
                        pump_price: null,
                        evidence_preview: null
                    });
                },

                removePumpPrice(index) {
                    if (confirm('Tem certeza que deseja remover este preço de bomba?')) {
                        this.formData.pumpPrices.splice(index, 1);
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

                handlePumpImageUpload(event, pumpIndex) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.formData.pumpPrices[pumpIndex].evidence_preview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                removePumpImage(pumpIndex) {
                    this.formData.pumpPrices[pumpIndex].evidence_preview = null;
                    // Limpar o input de arquivo
                    const input = document.querySelector(`input[name="pump_prices[${pumpIndex}][evidence]"]`);
                    if (input) input.value = '';
                },

                async submitForm(event) {
                    if (this.submitting) return;

                    if (this.formData.stations.length === 0) {
                        alert('Adicione pelo menos um posto para continuar.');
                        return;
                    }

                    this.submitting = true;

                    try {
                        const formElement = event.target;
                        const formData = new FormData(formElement);

                        // Adicionar dados básicos
                        formData.append('name', this.formData.name);
                        formData.append('quotation_date', this.formData.quotation_date);
                        formData.append('notes', this.formData.notes);

                        // Adicionar hidden inputs para stations
                        this.formData.stations.forEach((station, si) => {
                            formData.append(`stations[${si}][gas_station_id]`, station.gas_station_id);
                        });

                        // Adicionar hidden inputs para pumpPrices
                        this.formData.pumpPrices.forEach((pumpPrice, pi) => {
                            formData.append(`pump_prices[${pi}][gas_station_id]`, pumpPrice.gas_station_id);
                            formData.append(`pump_prices[${pi}][fuel_type_id]`, pumpPrice.fuel_type_id);
                            formData.append(`pump_prices[${pi}][pump_price]`, pumpPrice.pump_price);
                        });

                        const response = await fetch('{{ route('fuel-quotations.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            alert('Erro ao salvar cotação: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        alert('Erro ao salvar cotação: ' + error.message);
                    } finally {
                        this.submitting = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

