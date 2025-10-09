<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Multa" subtitle="Cadastrar nova multa de trânsito" hide-title-mobile icon="plus" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-ui.card>
                <form action="{{ route('fines.store') }}" method="POST" enctype="multipart/form-data"
                      x-data="fineForm()" @submit.prevent="submitForm">
                    @csrf

                    <!-- Auto de Infração -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <x-icon name="clipboard" class="w-5 h-5 inline" /> Auto de Infração
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Número do Auto (opcional)
                                </label>
                                <input type="text" name="infraction_notice_number"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                       placeholder="Digite ou deixe em branco para gerar automaticamente">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Autoridade Emissora
                                </label>
                                <input type="text" name="issuing_authority"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                       placeholder="Ex: DETRAN-DF">
                            </div>
                        </div>
                    </div>

                    <!-- Dados da Multa -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <x-icon name="car" class="w-5 h-5 inline" /> Dados da Multa
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Veículo *
                                </label>
                                <select name="vehicle_id" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">Selecione um veículo</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate }} - {{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Condutor *
                                </label>
                                <select name="driver_id" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">Selecione um condutor</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                @error('driver_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data da Infração *
                                </label>
                                <input type="datetime-local" name="issued_at" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                @error('issued_at')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data de Vencimento
                                </label>
                                <input type="date" name="due_date"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Local da Infração
                                </label>
                                <input type="text" name="location"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                       placeholder="Ex: Av. Paulista, 1000 - São Paulo/SP">
                            </div>
                        </div>
                    </div>

                    <!-- Infrações -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <x-icon name="list" class="w-5 h-5 inline" /> Infrações
                            </h3>
                            <button type="button" @click="addInfraction"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition">
                                <x-icon name="plus" class="w-4 h-4" />
                                Adicionar Infração
                            </button>
                        </div>

                        <template x-for="(infraction, index) in infractions" :key="index">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        Infração <span x-text="index + 1"></span>
                                    </h4>
                                    <button type="button" @click="removeInfraction(index)"
                                            x-show="infractions.length > 1"
                                            class="text-red-600 hover:text-red-700 text-sm">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Código *
                                        </label>
                                        <input type="text" :name="'infractions['+index+'][code]'" x-model="infraction.code" required
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                               placeholder="Ex: 501-00">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Descrição *
                                        </label>
                                        <input type="text" :name="'infractions['+index+'][description]'" x-model="infraction.description" required
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                               placeholder="Descrição da infração">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Valor Base *
                                        </label>
                                        <input type="number" step="0.01" :name="'infractions['+index+'][base_amount]'" x-model="infraction.base_amount" required
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Taxas Extras
                                        </label>
                                        <input type="number" step="0.01" :name="'infractions['+index+'][extra_fees]'" x-model="infraction.extra_fees"
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Desconto (R$)
                                        </label>
                                        <input type="number" step="0.01" :name="'infractions['+index+'][discount_amount]'" x-model="infraction.discount_amount"
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Desconto (%)
                                        </label>
                                        <input type="number" step="0.01" max="100" :name="'infractions['+index+'][discount_percentage]'" x-model="infraction.discount_percentage"
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Pontos CNH
                                        </label>
                                        <input type="number" :name="'infractions['+index+'][points]'" x-model="infraction.points"
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Gravidade *
                                        </label>
                                        <select :name="'infractions['+index+'][severity]'" x-model="infraction.severity" required
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                            <option value="leve">Leve</option>
                                            <option value="media">Média</option>
                                            <option value="grave">Grave</option>
                                            <option value="gravissima">Gravíssima</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Valor Final: R$ <span x-text="calculateFinalAmount(infraction).toFixed(2)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                Valor Total da Multa: R$ <span x-text="totalAmount.toFixed(2)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Anexos -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <x-icon name="upload" class="w-5 h-5 inline" /> Anexos
                        </h3>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <input type="file" name="attachments[]" multiple accept="image/*,application/pdf"
                                   class="hidden" id="file-upload">
                            <label for="file-upload" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Clique para selecionar ou arraste arquivos aqui
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    PDF, PNG, JPG até 10MB
                                </p>
                            </label>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('fines.index') }}"
                           class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition">
                            Cadastrar Multa
                        </button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>

    @push('scripts')
    <script>
        function fineForm() {
            return {
                infractions: [{
                    code: '',
                    description: '',
                    base_amount: 0,
                    extra_fees: 0,
                    discount_amount: 0,
                    discount_percentage: 0,
                    points: 0,
                    severity: 'media'
                }],

                get totalAmount() {
                    return this.infractions.reduce((sum, inf) => sum + this.calculateFinalAmount(inf), 0);
                },

                calculateFinalAmount(infraction) {
                    let amount = parseFloat(infraction.base_amount || 0) + parseFloat(infraction.extra_fees || 0);

                    if (infraction.discount_percentage > 0) {
                        amount -= (amount * (parseFloat(infraction.discount_percentage) / 100));
                    }

                    amount -= parseFloat(infraction.discount_amount || 0);

                    return Math.max(0, amount);
                },

                addInfraction() {
                    this.infractions.push({
                        code: '',
                        description: '',
                        base_amount: 0,
                        extra_fees: 0,
                        discount_amount: 0,
                        discount_percentage: 0,
                        points: 0,
                        severity: 'media'
                    });
                },

                removeInfraction(index) {
                    if (this.infractions.length > 1) {
                        this.infractions.splice(index, 1);
                    }
                },

                submitForm(e) {
                    e.target.submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

