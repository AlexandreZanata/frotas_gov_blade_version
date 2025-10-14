<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Posto" subtitle="Atualizar dados do posto" hide-title-mobile icon="fuel" />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('gas-stations.update', $gasStation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Nome do Posto <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $gasStation->name) }}"
                           required
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Endereço -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Endereço
                    </label>
                    <input type="text"
                           name="address"
                           id="address"
                           value="{{ old('address', $gasStation->address) }}"
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('address')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CNPJ -->
                <div>
                    <label for="cnpj" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        CNPJ
                    </label>
                    <input type="text"
                           name="cnpj"
                           id="cnpj"
                           value="{{ old('cnpj', $gasStation->cnpj ? \App\Http\Controllers\GasStationController::formatCnpj($gasStation->cnpj) : '') }}"
                           placeholder="00.000.000/0000-00"
                           maxlength="18"
                           class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('cnpj')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="active" {{ old('status', $gasStation->status) === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $gasStation->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('gas-stations.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-lg hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition">
                    Atualizar Posto
                </button>
            </div>
        </form>
    </x-ui.card>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cnpjInput = document.getElementById('cnpj');
                const cnpjFeedback = document.getElementById('cnpjFeedback');
                const form = document.getElementById('gasStationForm');

                let cnpjCheckTimeout = null;
                let isCnpjValid = false;
                let isCheckingCnpj = false;

                function formatCNPJ(value) {
                    const numbers = value.replace(/\D/g, '');
                    const limitedNumbers = numbers.substring(0, 14);

                    if (limitedNumbers.length === 14) {
                        return limitedNumbers.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
                    }

                    return limitedNumbers;
                }

                function showFeedback(message, type) {
                    cnpjFeedback.textContent = message;
                    cnpjFeedback.className = 'mt-1 text-sm ' + (type === 'error' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400');
                    cnpjFeedback.classList.remove('hidden');
                }

                function hideFeedback() {
                    cnpjFeedback.classList.add('hidden');
                }

                function checkCnpjAvailability(cnpj) {
                    if (cnpj.length !== 14 || isCheckingCnpj) {
                        hideFeedback();
                        isCnpjValid = false;
                        return;
                    }

                    isCheckingCnpj = true;

                    fetch('{{ route("gas-stations.check-cnpj") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            cnpj: cnpj,
                            exclude_id: '{{ $gasStation->id }}'
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                showFeedback(data.message, 'error');
                                isCnpjValid = false;
                            } else {
                                showFeedback(data.message, 'success');
                                isCnpjValid = true;
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao verificar CNPJ:', error);
                            isCnpjValid = false;
                        })
                        .finally(() => {
                            isCheckingCnpj = false;
                        });
                }

                if (cnpjInput) {
                    cnpjInput.addEventListener('input', function(e) {
                        const currentValue = e.target.value;
                        const numbersOnly = currentValue.replace(/\D/g, '');

                        // Se o usuário digitou 14 números, formata e verifica
                        if (numbersOnly.length === 14) {
                            e.target.value = formatCNPJ(currentValue);

                            // Verifica disponibilidade do CNPJ com debounce
                            clearTimeout(cnpjCheckTimeout);
                            cnpjCheckTimeout = setTimeout(() => {
                                checkCnpjAvailability(numbersOnly);
                            }, 800);
                        } else {
                            // Permite que o usuário digite/edit livremente até ter 14 números
                            e.target.value = numbersOnly;
                            hideFeedback();
                            isCnpjValid = false;
                        }
                    });

                    // Formata ao perder o foco, se tiver 14 números
                    cnpjInput.addEventListener('blur', function(e) {
                        const numbersOnly = e.target.value.replace(/\D/g, '');
                        if (numbersOnly.length === 14) {
                            e.target.value = formatCNPJ(e.target.value);
                            checkCnpjAvailability(numbersOnly);
                        }
                    });

                    // Remove formatação ao ganhar foco para facilitar edição
                    cnpjInput.addEventListener('focus', function(e) {
                        const numbersOnly = e.target.value.replace(/\D/g, '');
                        if (numbersOnly.length === 14) {
                            e.target.value = numbersOnly;
                        }
                    });

                    // Formatar valor inicial se existir
                    if (cnpjInput.value) {
                        const numbersOnly = cnpjInput.value.replace(/\D/g, '');
                        if (numbersOnly.length === 14) {
                            cnpjInput.value = formatCNPJ(cnpjInput.value);
                            checkCnpjAvailability(numbersOnly);
                        }
                    }
                }

                // REMOVER a validação no submit que estava causando o bug
            });
        </script>
    @endpush
</x-app-layout>
