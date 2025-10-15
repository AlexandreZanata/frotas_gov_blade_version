<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Registrar Abastecimento" subtitle="Preencha os dados do abastecimento" hide-title-mobile icon="fuel" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook.finish', $run)" icon="arrow-left" title="Voltar para Finalizar Corrida" variant="neutral" />
    </x-slot>

    <!-- Vehicle Info Card -->
    <div class="mb-6 bg-white dark:bg-navy-800 rounded-lg shadow p-6">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-full">
                <x-icon name="car" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">
                    {{ $run->vehicle->prefix->name ?? 'N/A' }} - {{ $run->vehicle->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-navy-300">
                    Placa: {{ $run->vehicle->plate }}
                </p>
            </div>
        </div>

        <!-- Trip Info -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-navy-700 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">KM Atual da Corrida</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ number_format($run->start_km, 0, ',', '.') }} km</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-navy-300">Destino Principal</p>
                <p class="text-base font-medium text-gray-900 dark:text-navy-50">{{ $run->destinations->first()->destination ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <x-ui.card title="Dados do Abastecimento" subtitle="Preencha as informações do abastecimento realizado">
        <form action="{{ route('logbook.store-fueling', $run) }}" method="POST" enctype="multipart/form-data"
              class="space-y-6"
              x-data="{
                isManual: false,
                liters: {{ old('liters', 0) }},
                pricePerLiter: {{ old('value_per_liter', 0) }},
                get totalValue() {
                    return (this.liters * this.pricePerLiter).toFixed(2);
                }
            }">
            @csrf

            <div class="space-y-6">
                <!-- Fueling Type Selection -->
                <div>
                    <x-input-label value="Tipo de Abastecimento *" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="is_manual" value="0" x-model="isManual" :value="false" class="sr-only peer" checked>
                            <div class="p-4 border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 border-gray-300 dark:border-navy-600 transition">
                                <p class="font-medium text-gray-900 dark:text-navy-50">Posto Credenciado</p>
                                <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">Valor calculado automaticamente</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="is_manual" value="1" x-model="isManual" :value="true" class="sr-only peer">
                            <div class="p-4 border-2 rounded-lg peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 border-gray-300 dark:border-navy-600 transition">
                                <p class="font-medium text-gray-900 dark:text-navy-50">Abastecimento Manual</p>
                                <p class="text-xs text-gray-500 dark:text-navy-400 mt-1">Preenchimento manual dos valores</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Gas Station (only for credenciado) -->
                <div x-show="!isManual" x-transition>
                    <x-input-label for="gas_station_id" value="Posto de Combustível *" />
                    <x-ui.select name="gas_station_id" id="gas_station_id" class="mt-2" required>
                        <option value="">Selecione o posto...</option>
                        @foreach($gasStations as $station)
                            <option value="{{ $station->id }}"
                                    @selected(old('gas_station_id') == $station->id)
                                    data-price="{{ $station->price_per_liter }}">
                                {{ $station->name }} - R$ {{ number_format($station->price_per_liter, 2, ',', '.') }}/L
                            </option>
                        @endforeach
                    </x-ui.select>
                    <x-input-error :messages="$errors->get('gas_station_id')" class="mt-2" />
                </div>

                <!-- Fuel Type -->
                <div>
                    <x-input-label for="fuel_type_id" value="Tipo de Combustível *" />
                    <x-ui.select name="fuel_type_id" id="fuel_type_id" class="mt-2" required>
                        <option value="">Selecione o combustível...</option>
                        @foreach($fuelTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('fuel_type_id') == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-2" />
                </div>

                <!-- KM -->
                <div>
                    <x-input-label for="km" value="KM no momento do abastecimento *" />
                    <div class="mt-2">
                        <input
                            type="number"
                            name="km"
                            id="km"
                            value="{{ old('km', $run->start_km) }}"
                            min="{{ $run->start_km }}"
                            step="1"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Informe a quilometragem atual"
                        >
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                        KM deve ser maior ou igual ao inicial da corrida ({{ number_format($run->start_km, 0, ',', '.') }} km)
                    </p>
                    <x-input-error :messages="$errors->get('km')" class="mt-2" />
                </div>

                <!-- Liters -->
                <div>
                    <x-input-label for="liters" value="Quantidade de Litros *" />
                    <div class="mt-2 relative">
                        <input
                            type="number"
                            name="liters"
                            id="liters"
                            x-model.number="liters"
                            value="{{ old('liters') }}"
                            step="0.001"
                            min="0.1"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pr-12"
                            placeholder="0.000"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-navy-300 text-sm font-medium">L</span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('liters')" class="mt-2" />
                </div>

                <!-- Price per Liter (manual only) -->
                <div x-show="isManual" x-transition>
                    <x-input-label for="value_per_liter" value="Valor por Litro (R$) *" />
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-navy-300">R$</span>
                        </div>
                        <input
                            type="number"
                            name="value_per_liter"
                            id="value_per_liter"
                            x-model.number="pricePerLiter"
                            value="{{ old('value_per_liter') }}"
                            step="0.01"
                            min="0"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pl-12"
                            placeholder="0.00"
                        >
                    </div>
                    <x-input-error :messages="$errors->get('value_per_liter')" class="mt-2" />
                </div>

                <!-- Gas Station Name (Manual) -->
                <div x-show="isManual" x-transition>
                    <x-input-label for="gas_station_name" value="Nome do Posto *" />
                    <div class="mt-2">
                        <input
                            type="text"
                            name="gas_station_name"
                            id="gas_station_name"
                            value="{{ old('gas_station_name') }}"
                            required
                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Ex: Posto Shell, Posto Ipiranga..."
                        >
                    </div>
                    <x-input-error :messages="$errors->get('gas_station_name')" class="mt-2" />
                </div>

                <!-- Total Value Display -->
                <div class="p-4 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-primary-700 dark:text-primary-300">Valor Total do Abastecimento:</span>
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            R$ <span x-text="totalValue"></span>
                        </span>
                    </div>
                </div>

                <!-- Invoice Upload -->
                <div>
                    <x-input-label for="invoice_path" value="Nota Fiscal (Opcional)" />
                    <div class="mt-2">
                        <input
                            type="file"
                            name="invoice_path"
                            id="invoice_path"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-gray-500 dark:text-navy-300 border border-gray-300 dark:border-navy-600 rounded-md cursor-pointer bg-gray-50 dark:bg-navy-700 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-400"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">
                        Formatos aceitos: PDF, JPG, JPEG ou PNG (tamanho máximo: 5MB)
                    </p>
                    <x-input-error :messages="$errors->get('invoice_path')" class="mt-2" />
                </div>

                <!-- Signature -->
                <div>
                    <x-input-label for="signature" value="Assinatura *" />
                    <div class="mt-2 border-2 border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700">
                        <canvas
                            id="signature-pad"
                            class="w-full h-48 cursor-crosshair"
                            x-data="signaturePad()"
                            x-init="init()"
                        ></canvas>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs text-gray-500 dark:text-navy-400">
                            Assine no campo acima para confirmar o abastecimento
                        </p>
                        <button
                            type="button"
                            @click="$refs.signaturePad.clear()"
                            class="text-sm text-gray-600 dark:text-navy-300 hover:text-gray-900 dark:hover:text-navy-50 underline"
                        >
                            Limpar Assinatura
                        </button>
                    </div>
                    <input type="hidden" name="signature_path" id="signature_path" required>
                    <x-input-error :messages="$errors->get('signature_path')" class="mt-2" />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('logbook.finish', $run) }}">
                    <x-secondary-button type="button">
                        <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                        Voltar para Finalizar Corrida
                    </x-secondary-button>
                </a>

                <x-primary-button type="submit">
                    <x-icon name="save" class="w-4 h-4 mr-2" />
                    Registrar Abastecimento
                </x-primary-button>
            </div>
        </form>
    </x-ui.card>

    @push('scripts')
        <script>
            function signaturePad() {
                return {
                    canvas: null,
                    ctx: null,
                    drawing: false,
                    init() {
                        this.canvas = document.getElementById('signature-pad');
                        this.ctx = this.canvas.getContext('2d');

                        // Set canvas size
                        this.canvas.width = this.canvas.offsetWidth;
                        this.canvas.height = this.canvas.offsetHeight;

                        // Style the signature area
                        this.ctx.strokeStyle = '#3b82f6';
                        this.ctx.lineWidth = 2;
                        this.ctx.lineCap = 'round';
                        this.ctx.lineJoin = 'round';

                        // Event listeners
                        this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                        this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                        this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                        this.canvas.addEventListener('mouseleave', () => this.stopDrawing());

                        // Touch events
                        this.canvas.addEventListener('touchstart', (e) => {
                            e.preventDefault();
                            this.startDrawing(e.touches[0]);
                        });
                        this.canvas.addEventListener('touchmove', (e) => {
                            e.preventDefault();
                            this.draw(e.touches[0]);
                        });
                        this.canvas.addEventListener('touchend', () => this.stopDrawing());
                    },
                    startDrawing(e) {
                        this.drawing = true;
                        this.ctx.beginPath();
                        const rect = this.canvas.getBoundingClientRect();
                        this.ctx.moveTo(
                            e.clientX - rect.left,
                            e.clientY - rect.top
                        );
                    },
                    draw(e) {
                        if (!this.drawing) return;
                        const rect = this.canvas.getBoundingClientRect();
                        this.ctx.lineTo(
                            e.clientX - rect.left,
                            e.clientY - rect.top
                        );
                        this.ctx.stroke();
                    },
                    stopDrawing() {
                        if (this.drawing) {
                            this.drawing = false;
                            document.getElementById('signature_path').value = this.canvas.toDataURL();
                        }
                    },
                    clear() {
                        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                        document.getElementById('signature_path').value = '';
                    }
                }
            }

            // Auto-update price when gas station is selected
            document.addEventListener('DOMContentLoaded', function() {
                const gasStationSelect = document.getElementById('gas_station_id');
                if (gasStationSelect) {
                    gasStationSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        const price = selectedOption.getAttribute('data-price');
                        if (price && !window.alpineData.isManual) {
                            window.alpineData.pricePerLiter = parseFloat(price);
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
