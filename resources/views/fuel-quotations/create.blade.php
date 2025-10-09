<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Cotação de Combustível" subtitle="Registro de preços e cálculo de médias" hide-title-mobile icon="trending-up" />
    </x-slot>

    <div x-data="fuelQuotationApp()" x-init="init()" class="space-y-6">
        <!-- Informações Básicas -->
        <x-ui.card title="Informações da Cotação">
            <form @submit.prevent="submitForm" id="quotationForm" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome da Cotação -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                            Nome da Cotação <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               x-model="formData.name"
                               @input="autoSave"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Data da Cotação -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                            Data da Cotação <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               x-model="formData.quotation_date"
                               @input="autoSave"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Método de Cálculo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                            Método de Cálculo <span class="text-red-500">*</span>
                        </label>
                        <select x-model="formData.calculation_method"
                                @change="calculateAverages(); autoSave()"
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="simple_average">Média Aritmética Simples</option>
                            <option value="custom">Método Personalizado</option>
                        </select>
                    </div>

                    <!-- Observações -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                            Observações
                        </label>
                        <textarea x-model="formData.notes"
                                  @input="autoSave"
                                  rows="3"
                                  class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                </div>
            </form>
        </x-ui.card>

        <!-- Coleta de Preços -->
        <x-ui.card title="1. Coleta de Preços (Base para Cálculo de Média)">
            <div class="space-y-4">
                <!-- Botão Adicionar Preço -->
                <button @click="addPrice"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Adicionar Posto
                </button>

                <!-- Lista de Preços -->
                <div class="space-y-3">
                    <template x-for="(price, index) in prices" :key="index">
                        <div class="p-4 bg-gray-50 dark:bg-navy-900/50 rounded-lg border border-gray-200 dark:border-navy-700">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <!-- Posto -->
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Posto <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="price.gas_station_id"
                                            @change="autoSave"
                                            required
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Selecione...</option>
                                        <template x-for="station in gasStations" :key="station.id">
                                            <option :value="station.id" x-text="station.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Tipo de Combustível -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Combustível <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="price.fuel_type_id"
                                            @change="calculateAverages(); autoSave()"
                                            required
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Selecione...</option>
                                        <template x-for="fuel in fuelTypes" :key="fuel.id">
                                            <option :value="fuel.id" x-text="fuel.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Preço -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Preço (R$/L) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           x-model="price.price"
                                           @input="calculateAverages(); autoSave()"
                                           step="0.001"
                                           min="0"
                                           required
                                           class="w-full text-sm rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>

                                <!-- Upload de Comprovante -->
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Comprovante
                                    </label>
                                    <input type="file"
                                           @change="handleFileUpload($event, 'price', index)"
                                           accept="image/*"
                                           class="w-full text-xs rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p x-show="price.evidencePreview" class="mt-1 text-xs text-green-600 dark:text-green-400">
                                        ✓ Arquivo anexado
                                    </p>
                                </div>

                                <!-- Botão Remover -->
                                <div class="md:col-span-2">
                                    <button @click="removePrice(index)"
                                            type="button"
                                            class="w-full px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm rounded-lg transition">
                                        Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="prices.length === 0" class="p-8 text-center text-gray-500 dark:text-navy-400">
                        Nenhum preço adicionado. Clique em "Adicionar Posto" para começar.
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Médias Calculadas e Descontos -->
        <x-ui.card title="2. Médias Calculadas e Aplicação de Descontos">
            <div class="space-y-4">
                <template x-for="(avg, fuelTypeId) in averages" :key="fuelTypeId">
                    <div class="p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                            <!-- Tipo de Combustível -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                    Combustível
                                </label>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="getFuelTypeName(fuelTypeId)"></p>
                            </div>

                            <!-- Média Calculada -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                    Preço Médio
                                </label>
                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                    R$ <span x-text="avg.toFixed(3).replace('.', ',')"></span>
                                </p>
                            </div>

                            <!-- Percentual de Desconto -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                    Desconto (%)
                                </label>
                                <input type="number"
                                       x-model="discounts[fuelTypeId]"
                                       @input="calculateFinalPrices(); autoSave()"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       class="w-full text-sm rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <!-- Valor do Desconto -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                    Valor Desc.
                                </label>
                                <p class="text-sm font-medium text-gray-700 dark:text-navy-200">
                                    - R$ <span x-text="((avg * (discounts[fuelTypeId] || 0)) / 100).toFixed(3).replace('.', ',')"></span>
                                </p>
                            </div>

                            <!-- Preço Final -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                    Preço Final
                                </label>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                    R$ <span x-text="finalPrices[fuelTypeId]?.toFixed(3).replace('.', ',') || '0,000'"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="Object.keys(averages).length === 0" class="p-8 text-center text-gray-500 dark:text-navy-400">
                    As médias serão calculadas automaticamente após adicionar preços.
                </div>
            </div>
        </x-ui.card>

        <!-- Preços de Bomba (Opcional) -->
        <x-ui.card title="3. Preços de Bomba para Comparação (Opcional)">
            <div class="space-y-4">
                <!-- Botão Adicionar Preço de Bomba -->
                <button @click="addPumpPrice"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Adicionar Preço de Bomba
                </button>

                <!-- Lista de Preços de Bomba -->
                <div class="space-y-3">
                    <template x-for="(pumpPrice, index) in pumpPrices" :key="index">
                        <div class="p-4 bg-gray-50 dark:bg-navy-900/50 rounded-lg border border-gray-200 dark:border-navy-700">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <!-- Posto -->
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Posto <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="pumpPrice.gas_station_id"
                                            @change="autoSave"
                                            required
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Selecione...</option>
                                        <template x-for="station in gasStations" :key="station.id">
                                            <option :value="station.id" x-text="station.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Tipo de Combustível -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Combustível <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="pumpPrice.fuel_type_id"
                                            @change="autoSave"
                                            required
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Selecione...</option>
                                        <template x-for="fuel in fuelTypes" :key="fuel.id">
                                            <option :value="fuel.id" x-text="fuel.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Preço de Bomba -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Preço (R$/L) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number"
                                           x-model="pumpPrice.pump_price"
                                           @input="autoSave"
                                           step="0.001"
                                           min="0"
                                           required
                                           class="w-full text-sm rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>

                                <!-- Upload de Comprovante -->
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 dark:text-navy-200 mb-1">
                                        Comprovante
                                    </label>
                                    <input type="file"
                                           @change="handleFileUpload($event, 'pump', index)"
                                           accept="image/*"
                                           class="w-full text-xs rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p x-show="pumpPrice.evidencePreview" class="mt-1 text-xs text-green-600 dark:text-green-400">
                                        ✓ Arquivo anexado
                                    </p>
                                </div>

                                <!-- Botão Remover -->
                                <div class="md:col-span-2">
                                    <button @click="removePumpPrice(index)"
                                            type="button"
                                            class="w-full px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm rounded-lg transition">
                                        Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </x-ui.card>

        <!-- Tabela Comparativa -->
        <x-ui.card title="4. Tabela Comparativa Final" x-show="Object.keys(finalPrices).length > 0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                    <thead class="bg-gray-50 dark:bg-navy-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Combustível</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Preço Médio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Desconto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Preço Final</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Preços de Bomba</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                        <template x-for="(finalPrice, fuelTypeId) in finalPrices" :key="fuelTypeId">
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" x-text="getFuelTypeName(fuelTypeId)"></td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-navy-200">
                                    R$ <span x-text="averages[fuelTypeId]?.toFixed(3).replace('.', ',')"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-navy-200">
                                    <span x-text="(discounts[fuelTypeId] || 0)"></span>%
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-green-600 dark:text-green-400">
                                    R$ <span x-text="finalPrice.toFixed(3).replace('.', ',')"></span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <template x-for="pump in getPumpPricesByFuel(fuelTypeId)">
                                        <div class="mb-1">
                                            <span class="text-gray-700 dark:text-navy-200" x-text="getStationName(pump.gas_station_id)"></span>:
                                            <span class="font-medium">R$ <span x-text="parseFloat(pump.pump_price).toFixed(3).replace('.', ',')"></span></span>
                                        </div>
                                    </template>
                                    <span x-show="getPumpPricesByFuel(fuelTypeId).length === 0" class="text-gray-400">—</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <template x-for="pump in getPumpPricesByFuel(fuelTypeId)">
                                        <div class="mb-1">
                                            <span :class="parseFloat(pump.pump_price) > finalPrice ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-red-600 dark:text-red-400 font-semibold'">
                                                <span x-text="parseFloat(pump.pump_price) > finalPrice ? '✓ Favorável' : '✗ Desfavorável'"></span>
                                                (<span x-text="(((parseFloat(pump.pump_price) - finalPrice) / finalPrice) * 100).toFixed(2)"></span>%)
                                            </span>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Botões de Ação -->
        <div class="flex items-center justify-between bg-white dark:bg-navy-800 p-6 rounded-lg shadow">
            <div>
                <p class="text-sm text-gray-600 dark:text-navy-300">
                    <span x-show="lastSaved">Salvo automaticamente às <span x-text="lastSaved"></span></span>
                    <span x-show="!lastSaved">Os dados são salvos automaticamente</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('fuel-quotations.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                    Cancelar
                </a>
                <button @click="clearLocalStorage"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                    Limpar Dados
                </button>
                <button @click="submitForm"
                        type="button"
                        :disabled="!canSubmit()"
                        :class="canSubmit() ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="px-6 py-2 text-sm font-medium text-white rounded-lg shadow transition">
                    Finalizar Cotação
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function fuelQuotationApp() {
            return {
                // Dados
                gasStations: @json($gasStations),
                fuelTypes: @json($fuelTypes),

                // Formulário
                formData: {
                    name: '',
                    quotation_date: new Date().toISOString().split('T')[0],
                    calculation_method: 'simple_average',
                    notes: ''
                },

                // Preços coletados
                prices: [],

                // Preços de bomba
                pumpPrices: [],

                // Médias calculadas
                averages: {},

                // Descontos
                discounts: {},

                // Preços finais
                finalPrices: {},

                // Controle
                lastSaved: null,
                autoSaveTimeout: null,

                init() {
                    this.loadFromLocalStorage();
                    console.log('Fuel Quotation App initialized');
                },

                addPrice() {
                    this.prices.push({
                        gas_station_id: '',
                        fuel_type_id: '',
                        price: '',
                        evidence: null,
                        evidencePreview: null
                    });
                    this.autoSave();
                },

                removePrice(index) {
                    this.prices.splice(index, 1);
                    this.calculateAverages();
                    this.autoSave();
                },

                addPumpPrice() {
                    this.pumpPrices.push({
                        gas_station_id: '',
                        fuel_type_id: '',
                        pump_price: '',
                        evidence: null,
                        evidencePreview: null
                    });
                    this.autoSave();
                },

                removePumpPrice(index) {
                    this.pumpPrices.splice(index, 1);
                    this.autoSave();
                },

                handleFileUpload(event, type, index) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (type === 'price') {
                        this.prices[index].evidence = file;
                        this.prices[index].evidencePreview = URL.createObjectURL(file);
                    } else {
                        this.pumpPrices[index].evidence = file;
                        this.pumpPrices[index].evidencePreview = URL.createObjectURL(file);
                    }

                    this.autoSave();
                },

                calculateAverages() {
                    this.averages = {};

                    // Agrupar preços por tipo de combustível
                    const pricesByFuel = {};

                    this.prices.forEach(price => {
                        if (price.fuel_type_id && price.price) {
                            if (!pricesByFuel[price.fuel_type_id]) {
                                pricesByFuel[price.fuel_type_id] = [];
                            }
                            pricesByFuel[price.fuel_type_id].push(parseFloat(price.price));
                        }
                    });

                    // Calcular média
                    Object.keys(pricesByFuel).forEach(fuelTypeId => {
                        const prices = pricesByFuel[fuelTypeId];
                        const sum = prices.reduce((a, b) => a + b, 0);
                        this.averages[fuelTypeId] = sum / prices.length;

                        // Inicializar desconto se não existir
                        if (!this.discounts[fuelTypeId]) {
                            this.discounts[fuelTypeId] = 0;
                        }
                    });

                    this.calculateFinalPrices();
                },

                calculateFinalPrices() {
                    this.finalPrices = {};

                    Object.keys(this.averages).forEach(fuelTypeId => {
                        const avg = this.averages[fuelTypeId];
                        const discount = this.discounts[fuelTypeId] || 0;
                        this.finalPrices[fuelTypeId] = avg - (avg * (discount / 100));
                    });
                },

                getFuelTypeName(fuelTypeId) {
                    const fuel = this.fuelTypes.find(f => f.id === fuelTypeId);
                    return fuel ? fuel.name : '';
                },

                getStationName(stationId) {
                    const station = this.gasStations.find(s => s.id === stationId);
                    return station ? station.name : '';
                },

                getPumpPricesByFuel(fuelTypeId) {
                    return this.pumpPrices.filter(p => p.fuel_type_id === fuelTypeId && p.pump_price);
                },

                canSubmit() {
                    return this.formData.name &&
                           this.formData.quotation_date &&
                           this.prices.length > 0 &&
                           Object.keys(this.averages).length > 0;
                },

                autoSave() {
                    clearTimeout(this.autoSaveTimeout);
                    this.autoSaveTimeout = setTimeout(() => {
                        this.saveToLocalStorage();
                        this.lastSaved = new Date().toLocaleTimeString('pt-BR');
                    }, 1000);
                },

                saveToLocalStorage() {
                    const data = {
                        formData: this.formData,
                        prices: this.prices.map(p => ({
                            gas_station_id: p.gas_station_id,
                            fuel_type_id: p.fuel_type_id,
                            price: p.price
                        })),
                        pumpPrices: this.pumpPrices.map(p => ({
                            gas_station_id: p.gas_station_id,
                            fuel_type_id: p.fuel_type_id,
                            pump_price: p.pump_price
                        })),
                        discounts: this.discounts
                    };

                    localStorage.setItem('fuelQuotationDraft', JSON.stringify(data));
                },

                loadFromLocalStorage() {
                    const saved = localStorage.getItem('fuelQuotationDraft');
                    if (saved) {
                        try {
                            const data = JSON.parse(saved);
                            this.formData = data.formData || this.formData;
                            this.prices = data.prices || [];
                            this.pumpPrices = data.pumpPrices || [];
                            this.discounts = data.discounts || {};
                            this.calculateAverages();
                        } catch (e) {
                            console.error('Erro ao carregar dados salvos:', e);
                        }
                    }
                },

                clearLocalStorage() {
                    if (confirm('Tem certeza que deseja limpar todos os dados? Esta ação não pode ser desfeita.')) {
                        localStorage.removeItem('fuelQuotationDraft');
                        location.reload();
                    }
                },

                async submitForm() {
                    if (!this.canSubmit()) {
                        alert('Preencha todos os campos obrigatórios antes de finalizar.');
                        return;
                    }

                    const formData = new FormData();

                    // Dados básicos
                    formData.append('name', this.formData.name);
                    formData.append('quotation_date', this.formData.quotation_date);
                    formData.append('calculation_method', this.formData.calculation_method);
                    formData.append('notes', this.formData.notes || '');

                    // Preços
                    this.prices.forEach((price, index) => {
                        formData.append(`prices[${index}][gas_station_id]`, price.gas_station_id);
                        formData.append(`prices[${index}][fuel_type_id]`, price.fuel_type_id);
                        formData.append(`prices[${index}][price]`, price.price);
                        if (price.evidence) {
                            formData.append(`prices[${index}][evidence]`, price.evidence);
                        }
                    });

                    // Descontos
                    Object.keys(this.discounts).forEach((fuelTypeId, index) => {
                        formData.append(`discounts[${index}][fuel_type_id]`, fuelTypeId);
                        formData.append(`discounts[${index}][discount_percentage]`, this.discounts[fuelTypeId]);
                    });

                    // Preços de bomba
                    this.pumpPrices.forEach((pump, index) => {
                        if (pump.gas_station_id && pump.fuel_type_id && pump.pump_price) {
                            formData.append(`pump_prices[${index}][gas_station_id]`, pump.gas_station_id);
                            formData.append(`pump_prices[${index}][fuel_type_id]`, pump.fuel_type_id);
                            formData.append(`pump_prices[${index}][pump_price]`, pump.pump_price);
                            if (pump.evidence) {
                                formData.append(`pump_prices[${index}][evidence]`, pump.evidence);
                            }
                        }
                    });

                    try {
                        const response = await fetch('{{ route("fuel-quotations.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: formData
                        });

                        if (response.ok) {
                            localStorage.removeItem('fuelQuotationDraft');
                            window.location.href = '{{ route("fuel-quotations.index") }}';
                        } else {
                            const error = await response.text();
                            alert('Erro ao salvar cotação: ' + error);
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        alert('Erro ao salvar cotação. Verifique sua conexão e tente novamente.');
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

