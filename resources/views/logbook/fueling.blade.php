<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Abastecimento') }}
        </h2>
    </x-slot>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Vehicle Info -->
            <x-ui.card>
                <div class="flex items-center gap-4">
                    <x-icon name="car" class="w-10 h-10 text-primary-600 dark:text-primary-400" />
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50">
                            {{ $run->vehicle->prefix->name ?? 'N/A' }} - {{ $run->vehicle->name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-navy-300">
                            Placa: {{ $run->vehicle->plate }}
                        </p>
                </div>
            </x-ui.card>

            <!-- Fueling Form -->
            <x-ui.card
                title="Dados do Abastecimento"
                subtitle="Preencha as informações do abastecimento realizado"
            >
                <form
                    action="{{ route('logbook.store-fueling', $run) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    x-data="{
                        isManual: false,
                        liters: 0,
                        pricePerLiter: 0,
                        get totalValue() {
                            return (this.liters * this.pricePerLiter).toFixed(2);
                        }
                    }"
                >
                    @csrf

                    <div class="space-y-6">
                        <!-- Fueling Type Toggle -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-navy-900 rounded-lg">
                            <div>
                                <label class="text-sm font-medium text-gray-900 dark:text-navy-50">Tipo de Abastecimento</label>
                                <p class="text-xs text-gray-500 dark:text-navy-300 mt-1">Escolha entre posto credenciado ou manual</p>
                            </div>
                            <button
                                type="button"
                                @click="isManual = !isManual"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                :class="isManual ? 'bg-primary-600' : 'bg-gray-200 dark:bg-navy-700'"
                            >
                                <span
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="isManual ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>

                        <input type="hidden" name="is_manual" x-model="isManual">

                        <!-- Gas Station (only for credenciado) -->
                        <div x-show="!isManual" x-transition>
                            <x-input-label for="gas_station_id" value="Posto de Combustível *" />
                            <x-ui.select name="gas_station_id" id="gas_station_id" placeholder="Selecione o posto" class="mt-2">
                                @foreach($gasStations as $station)
                                    <option value="{{ $station->id }}" data-price="{{ $station->price_per_liter }}">
                                        {{ $station->name }} - R$ {{ number_format($station->price_per_liter, 2, ',', '.') }}/L
                                    </option>
                                @endforeach
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('gas_station_id')" class="mt-2" />
                        </div>

                        <!-- Fuel Type -->
                        <div>
                            <x-input-label for="fuel_type_id" value="Tipo de Combustível *" />
                            <x-ui.select name="fuel_type_id" id="fuel_type_id" placeholder="Selecione o combustível" class="mt-2" required>
                                @foreach($fuelTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </x-ui.select>
                            <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-2" />
                        </div>

                        <!-- KM -->
                        <x-ui.km-input
                            name="km"
                            label="KM no momento do abastecimento *"
                            :value="old('km', $run->end_km)"
                            required
                        />

                        <!-- Liters -->
                        <div class="space-y-2">
                            <x-input-label for="liters" value="Quantidade de Litros *" />
                            <div class="relative">
                                <input
                                    type="number"
                                    name="liters"
                                    id="liters"
                                    x-model="liters"
                                    step="0.001"
                                    min="0.1"
                                    class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pr-12"
                                    placeholder="0.000"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 dark:text-navy-300 text-sm font-medium">L</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('liters')" />
                        </div>

                        <!-- Price per Liter (manual only) -->
                        <div x-show="isManual" x-transition>
                            <x-input-label for="value_per_liter" value="Valor por Litro *" />
                            <div class="relative mt-2">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-gray-500 dark:text-navy-300 text-sm">R$</span>
                                </div>
                                <input
                                    type="number"
                                    name="value_per_liter"
                                    id="value_per_liter"
                                    x-model="pricePerLiter"
                                    step="0.01"
                                    min="0"
                                    class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 pl-12"
                                    placeholder="0.00"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('value_per_liter')" />
                        </div>

                        <!-- Total Value Display -->
                        <div class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-navy-200">Valor Total:</span>
                                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    R$ <span x-text="totalValue"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Invoice Upload -->
                        <div class="space-y-2">
                            <x-input-label for="invoice_path" value="Nota Fiscal (Opcional)" />
                            <input
                                type="file"
                                name="invoice_path"
                                id="invoice_path"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full text-sm text-gray-900 dark:text-navy-50 border border-gray-300 dark:border-navy-600 rounded-md cursor-pointer bg-gray-50 dark:bg-navy-700 focus:outline-none"
                            >
                            <p class="text-xs text-gray-500 dark:text-navy-400">PDF, JPG, JPEG ou PNG (máx. 5MB)</p>
                            <x-input-error :messages="$errors->get('invoice_path')" />
                        </div>

                        <!-- Signature -->
                        <div class="space-y-2">
                            <x-input-label for="signature" value="Assinatura *" />
                            <div class="border-2 border-gray-300 dark:border-navy-600 rounded-lg">
                                <canvas
                                    id="signature-pad"
                                    class="w-full h-48 cursor-crosshair bg-white dark:bg-navy-700"
                                    x-data="signaturePad()"
                                    x-init="init()"
                                ></canvas>
                            </div>
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    @click="$refs.signaturePad.clear()"
                                    class="text-sm text-gray-600 dark:text-navy-300 hover:text-gray-900 dark:hover:text-navy-50"
                                >
                                    Limpar Assinatura
                                </button>
                            </div>
                            <input type="hidden" name="signature_path" id="signature_path" required>
                            <x-input-error :messages="$errors->get('signature_path')" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('logbook.index') }}">
                            <x-secondary-button>
                                Pular Abastecimento
                            </x-secondary-button>
                        </a>

                        <x-primary-button type="submit">
                            <x-icon name="save" class="w-4 h-4 mr-2" />
                            Registrar Abastecimento
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

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

                    // Event listeners
                    this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
                    this.canvas.addEventListener('mousemove', (e) => this.draw(e));
                    this.canvas.addEventListener('mouseup', () => this.stopDrawing());
                    this.canvas.addEventListener('mouseleave', () => this.stopDrawing());

                    // Touch events
                    this.canvas.addEventListener('touchstart', (e) => this.startDrawing(e.touches[0]));
                    this.canvas.addEventListener('touchmove', (e) => {
                        e.preventDefault();
                        this.draw(e.touches[0]);
                    });
                    this.canvas.addEventListener('touchend', () => this.stopDrawing());
                },
                startDrawing(e) {
                    this.drawing = true;
                    this.ctx.beginPath();
                    this.ctx.moveTo(e.offsetX || e.clientX - this.canvas.offsetLeft,
                                   e.offsetY || e.clientY - this.canvas.offsetTop);
                },
                draw(e) {
                    if (!this.drawing) return;
                    this.ctx.lineTo(e.offsetX || e.clientX - this.canvas.offsetLeft,
                                   e.offsetY || e.clientY - this.canvas.offsetTop);
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
    </script>
    @endpush
</x-app-layout>
