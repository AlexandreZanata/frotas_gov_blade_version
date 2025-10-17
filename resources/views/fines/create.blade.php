<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Multa" subtitle="Cadastrar nova multa de trânsito" hide-title-mobile icon="speed-camera" />
    </x-slot>

    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('fines.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Cadastro de Multa">
        <form action="{{ route('fines.store') }}" method="POST" enctype="multipart/form-data" id="fine-form">
            @csrf

            <!-- Auto de Infração -->
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="clipboard" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    Auto de Infração
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="infraction_notice_number" value="Número do Auto (opcional)" />
                        <x-text-input
                            id="infraction_notice_number"
                            name="infraction_notice_number"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('infraction_notice_number')"
                            placeholder="Digite ou deixe em branco para gerar automaticamente"
                        />
                    </div>
                    <div>
                        <x-input-label for="issuing_authority" value="Autoridade Emissora" />
                        <x-text-input
                            id="issuing_authority"
                            name="issuing_authority"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('issuing_authority')"
                            placeholder="Ex: DETRAN-DF"
                        />
                    </div>

                    <!-- Descrição da Multa -->
                    <div class="md:col-span-2">
                        <x-input-label for="description" value="Descrição da Multa *" />
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Descreva os detalhes da multa..."
                            required
                        >{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Dados da Multa -->
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="car" class="w-5 h-5 text-green-600 dark:text-green-400" />
                    Dados da Multa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="vehicle_id" value="Veículo *" />
                        <x-ui.select name="vehicle_id" id="vehicle_id" class="mt-1" required>
                            <option value="">Selecione um veículo</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                    {{ $vehicle->plate }} - {{ $vehicle->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="driver_id" value="Condutor *" />
                        <x-ui.select name="driver_id" id="driver_id" class="mt-1" required>
                            <option value="">Selecione um condutor</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>
                                    {{ $driver->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('driver_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="issued_at" value="Data da Infração *" />
                        <x-text-input
                            id="issued_at"
                            name="issued_at"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            :value="old('issued_at')"
                            required
                        />
                        <x-input-error :messages="$errors->get('issued_at')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="due_date" value="Data de Vencimento" />
                        <x-text-input
                            id="due_date"
                            name="due_date"
                            type="date"
                            class="mt-1 block w-full"
                            :value="old('due_date')"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="location" value="Local da Infração" />
                        <x-text-input
                            id="location"
                            name="location"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('location')"
                            placeholder="Ex: Av. Paulista, 1000 - São Paulo/SP"
                        />
                    </div>
                </div>
            </div>

            <!-- Infrações -->
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <x-icon name="list" class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                        Infrações
                    </h3>
                    <button type="button" id="add-infraction"
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-md transition shadow-sm">
                        <x-icon name="plus" class="w-4 h-4" />
                        Adicionar Infração
                    </button>
                </div>

                <div id="infractions-container">
                    <!-- Primeira infração -->
                    <div class="infraction-item bg-gray-50 dark:bg-navy-800/50 rounded-lg p-4 mb-4 border border-gray-200 dark:border-navy-600">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                Infração 1
                            </h4>
                            <button type="button" class="remove-infraction text-red-600 hover:text-red-700 text-sm transition" style="display: none;">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="infraction_code_0" value="Código *" />
                                <x-text-input
                                    id="infraction_code_0"
                                    name="infractions[0][code]"
                                    type="text"
                                    class="mt-1 block w-full infraction-code"
                                    placeholder="Ex: 501-00"
                                    required
                                />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="infraction_description_0" value="Descrição *" />
                                <x-text-input
                                    id="infraction_description_0"
                                    name="infractions[0][description]"
                                    type="text"
                                    class="mt-1 block w-full infraction-description"
                                    placeholder="Descrição da infração"
                                    required
                                />
                            </div>
                            <div>
                                <x-input-label for="infraction_base_amount_0" value="Valor Base *" />
                                <x-text-input
                                    id="infraction_base_amount_0"
                                    name="infractions[0][base_amount]"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full infraction-base-amount"
                                    required
                                />
                            </div>
                            <div>
                                <x-input-label for="infraction_extra_fees_0" value="Taxas Extras" />
                                <x-text-input
                                    id="infraction_extra_fees_0"
                                    name="infractions[0][extra_fees]"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full infraction-extra-fees"
                                />
                            </div>
                            <div>
                                <x-input-label for="infraction_discount_amount_0" value="Desconto (R$)" />
                                <x-text-input
                                    id="infraction_discount_amount_0"
                                    name="infractions[0][discount_amount]"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full infraction-discount-amount"
                                />
                            </div>
                            <div>
                                <x-input-label for="infraction_discount_percentage_0" value="Desconto (%)" />
                                <x-text-input
                                    id="infraction_discount_percentage_0"
                                    name="infractions[0][discount_percentage]"
                                    type="number"
                                    step="0.01"
                                    max="100"
                                    class="mt-1 block w-full infraction-discount-percentage"
                                />
                            </div>
                            <div>
                                <x-input-label for="infraction_points_0" value="Pontos CNH" />
                                <x-text-input
                                    id="infraction_points_0"
                                    name="infractions[0][points]"
                                    type="number"
                                    class="mt-1 block w-full infraction-points"
                                />
                            </div>
                            <div>
                                <x-input-label for="infraction_severity_0" value="Gravidade *" />
                                <x-ui.select
                                    id="infraction_severity_0"
                                    name="infractions[0][severity]"
                                    class="mt-1 infraction-severity"
                                    required
                                >
                                    <option value="leve">Leve</option>
                                    <option value="media" selected>Média</option>
                                    <option value="grave">Grave</option>
                                    <option value="gravissima">Gravíssima</option>
                                </x-ui.select>
                            </div>
                            <div class="flex items-end">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 p-2 bg-gray-100 dark:bg-navy-700 rounded-md w-full text-center infraction-final-amount">
                                    Valor Final: R$ 0.00
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
                    <div class="text-lg font-bold text-primary-900 dark:text-primary-100">
                        Valor Total da Multa: R$ <span id="total-amount">0.00</span>
                    </div>
                </div>
            </div>

            <!-- Anexos -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="upload" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    Anexos
                </h3>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-primary-400 dark:hover:border-primary-500 transition-colors">
                    <input type="file" name="attachments[]" multiple accept="image/*,application/pdf"
                           class="hidden" id="file-upload">
                    <label for="file-upload" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Clique para selecionar ou arraste arquivos aqui
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            PDF, PNG, JPG até 10MB
                        </p>
                    </label>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('fines.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">
                    Cancelar
                </a>
                <x-primary-button icon="save" compact>
                    Cadastrar Multa
                </x-primary-button>
            </div>
        </form>
    </x-ui.card>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let infractionCount = 1;
            const infractionsContainer = document.getElementById('infractions-container');
            const addButton = document.getElementById('add-infraction');
            const totalAmountElement = document.getElementById('total-amount');

            // Função para calcular o valor final de uma infração
            function calculateFinalAmount(infractionItem) {
                const baseAmount = parseFloat(infractionItem.querySelector('.infraction-base-amount').value) || 0;
                const extraFees = parseFloat(infractionItem.querySelector('.infraction-extra-fees').value) || 0;
                const discountAmount = parseFloat(infractionItem.querySelector('.infraction-discount-amount').value) || 0;
                const discountPercentage = parseFloat(infractionItem.querySelector('.infraction-discount-percentage').value) || 0;

                let amount = baseAmount + extraFees;

                if (discountPercentage > 0) {
                    amount -= (amount * discountPercentage / 100);
                }

                amount -= discountAmount;

                return Math.max(0, amount);
            }

            // Função para atualizar todos os valores
            function updateAllAmounts() {
                const infractionItems = document.querySelectorAll('.infraction-item');
                let total = 0;

                infractionItems.forEach(item => {
                    const finalAmount = calculateFinalAmount(item);
                    item.querySelector('.infraction-final-amount').textContent = `Valor Final: R$ ${finalAmount.toFixed(2)}`;
                    total += finalAmount;
                });

                totalAmountElement.textContent = total.toFixed(2);
            }

            // Adicionar evento de input para todos os campos numéricos
            function attachInputListeners(infractionItem) {
                const inputs = infractionItem.querySelectorAll('input[type="number"]');
                inputs.forEach(input => {
                    input.addEventListener('input', updateAllAmounts);
                });
            }

            // Adicionar nova infração
            addButton.addEventListener('click', function() {
                const newIndex = infractionCount;
                infractionCount++;

                const newInfraction = document.createElement('div');
                newInfraction.className = 'infraction-item bg-gray-50 dark:bg-navy-800/50 rounded-lg p-4 mb-4 border border-gray-200 dark:border-navy-600';
                newInfraction.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                            Infração ${newIndex + 1}
                        </h4>
                        <button type="button" class="remove-infraction text-red-600 hover:text-red-700 text-sm transition">
                            <x-icon name="trash" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="infraction_code_${newIndex}" value="Código *" />
                            <x-text-input
                                id="infraction_code_${newIndex}"
                                name="infractions[${newIndex}][code]"
                                type="text"
                                class="mt-1 block w-full infraction-code"
                                placeholder="Ex: 501-00"
                                required
                            />
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="infraction_description_${newIndex}" value="Descrição *" />
                            <x-text-input
                                id="infraction_description_${newIndex}"
                                name="infractions[${newIndex}][description]"
                                type="text"
                                class="mt-1 block w-full infraction-description"
                                placeholder="Descrição da infração"
                                required
                            />
                        </div>
                        <div>
                            <x-input-label for="infraction_base_amount_${newIndex}" value="Valor Base *" />
                            <x-text-input
                                id="infraction_base_amount_${newIndex}"
                                name="infractions[${newIndex}][base_amount]"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full infraction-base-amount"
                                required
                            />
                        </div>
                        <div>
                            <x-input-label for="infraction_extra_fees_${newIndex}" value="Taxas Extras" />
                            <x-text-input
                                id="infraction_extra_fees_${newIndex}"
                                name="infractions[${newIndex}][extra_fees]"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full infraction-extra-fees"
                            />
                        </div>
                        <div>
                            <x-input-label for="infraction_discount_amount_${newIndex}" value="Desconto (R$)" />
                            <x-text-input
                                id="infraction_discount_amount_${newIndex}"
                                name="infractions[${newIndex}][discount_amount]"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full infraction-discount-amount"
                            />
                        </div>
                        <div>
                            <x-input-label for="infraction_discount_percentage_${newIndex}" value="Desconto (%)" />
                            <x-text-input
                                id="infraction_discount_percentage_${newIndex}"
                                name="infractions[${newIndex}][discount_percentage]"
                                type="number"
                                step="0.01"
                                max="100"
                                class="mt-1 block w-full infraction-discount-percentage"
                            />
                        </div>
                        <div>
                            <x-input-label for="infraction_points_${newIndex}" value="Pontos CNH" />
                            <x-text-input
                                id="infraction_points_${newIndex}"
                                name="infractions[${newIndex}][points]"
                                type="number"
                                class="mt-1 block w-full infraction-points"
                            />
                        </div>
                        <div>
                            <x-input-label for="infraction_severity_${newIndex}" value="Gravidade *" />
                            <x-ui.select
                                id="infraction_severity_${newIndex}"
                                name="infractions[${newIndex}][severity]"
                                class="mt-1 infraction-severity"
                                required
                            >
                                <option value="leve">Leve</option>
                                <option value="media" selected>Média</option>
                                <option value="grave">Grave</option>
                                <option value="gravissima">Gravíssima</option>
                            </x-ui.select>
                        </div>
                        <div class="flex items-end">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 p-2 bg-gray-100 dark:bg-navy-700 rounded-md w-full text-center infraction-final-amount">
                                Valor Final: R$ 0.00
                            </div>
                        </div>
                    </div>
                `;

                infractionsContainer.appendChild(newInfraction);

                // Mostrar botão de remover na primeira infração se houver mais de uma
                if (infractionCount > 1) {
                    document.querySelectorAll('.remove-infraction').forEach(btn => {
                        btn.style.display = 'block';
                    });
                }

                // Anexar listeners aos novos inputs
                attachInputListeners(newInfraction);

                // Adicionar evento de remoção
                newInfraction.querySelector('.remove-infraction').addEventListener('click', function() {
                    newInfraction.remove();
                    infractionCount--;
                    updateAllAmounts();

                    // Esconder botão de remover se só tiver uma infração
                    if (infractionCount === 1) {
                        document.querySelector('.remove-infraction').style.display = 'none';
                    }
                });
            });

            // Anexar listeners à infração inicial
            attachInputListeners(document.querySelector('.infraction-item'));

            // Adicionar evento de remoção à infração inicial
            document.querySelector('.remove-infraction').addEventListener('click', function() {
                if (infractionCount > 1) {
                    this.closest('.infraction-item').remove();
                    infractionCount--;
                    updateAllAmounts();

                    if (infractionCount === 1) {
                        document.querySelector('.remove-infraction').style.display = 'none';
                    }
                }
            });

            // Validação do formulário
            document.getElementById('fine-form').addEventListener('submit', function(e) {
                const infractionItems = document.querySelectorAll('.infraction-item');
                let isValid = true;

                infractionItems.forEach((item, index) => {
                    const code = item.querySelector('.infraction-code').value;
                    const description = item.querySelector('.infraction-description').value;
                    const baseAmount = item.querySelector('.infraction-base-amount').value;

                    if (!code || !description || !baseAmount) {
                        isValid = false;
                        showNotification('❌ Erro', `Preencha todos os campos obrigatórios da infração ${index + 1}`, 'error');
                    }
                });

                // Validar descrição geral da multa
                const generalDescription = document.getElementById('description').value;
                if (!generalDescription) {
                    isValid = false;
                    showNotification('❌ Erro', 'Preencha a descrição geral da multa', 'error');
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function showNotification(title, message, type = 'success') {
                const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-700 dark:text-green-400' : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-400';

                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 ${bgColor} border px-4 py-3 rounded-lg shadow-lg max-w-md animate-slide-in`;
                notification.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-1">
                            <strong class="font-bold block">${title}</strong>
                            <span class="block text-sm mt-1">${message}</span>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-current opacity-70 hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;

                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }, 4000);
            }
        });
    </script>
</x-app-layout>
