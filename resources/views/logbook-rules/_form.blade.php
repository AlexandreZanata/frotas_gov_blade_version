{{-- resources/views/logbook-rules/_form.blade.php --}}
@csrf
<div class="grid gap-4 md:grid-cols-2">
    <!-- Nome da Regra -->
    <div>
        <x-input-label for="name" value="Nome da Regra *" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $logbookRule->name ?? '')"
                      placeholder="Ex: Limite Global Padrão" required />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <!-- Tipo de Alvo -->
    <div>
        <x-input-label for="target_type" value="Aplicar Para *" />
        <x-ui.select name="target_type" id="target_type" class="mt-1" required
                     x-data="{}"
                     @change="updateTargetOptions()">
            <option value="">Selecione...</option>
            <option value="global" @selected(old('target_type', $logbookRule->target_type ?? '') == 'global')>Global (Todos)</option>
            <option value="vehicle_category" @selected(old('target_type', $logbookRule->target_type ?? '') == 'vehicle_category')>Categoria de Veículo</option>
            <option value="user" @selected(old('target_type', $logbookRule->target_type ?? '') == 'user')>Usuário Específico</option>
            <option value="vehicle" @selected(old('target_type', $logbookRule->target_type ?? '') == 'vehicle')>Veículo Específico</option>
        </x-ui.select>
        <x-input-error :messages="$errors->get('target_type')" class="mt-1" />
    </div>

    <!-- Alvo Específico (Dinâmico) -->
    <div id="target_specific_container" style="display: none;" class="md:col-span-2">
        <x-input-label for="target_search" value="Selecionar Alvo *" />
        <div x-data="targetSearch()" class="relative">
            <input
                type="text"
                id="target_search"
                x-model="searchQuery"
                @input.debounce.300ms="search()"
                @focus="showDropdown = true"
                @click.away="closeDropdown()"
                placeholder="Digite para pesquisar..."
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                autocomplete="off"
            />
            <input type="hidden" name="target_id" id="target_id" x-model="selectedId">

            <!-- Dropdown de resultados -->
            <div x-show="showDropdown && (results.length > 0 || searchQuery.length > 0)"
                 x-cloak
                 x-transition
                 class="absolute z-50 w-full mt-1 bg-white dark:bg-navy-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto">

                <!-- Loading -->
                <div x-show="loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <x-icon name="refresh" class="w-4 h-4 animate-spin" />
                        <span>Pesquisando...</span>
                    </div>
                </div>

                <!-- Resultados -->
                <template x-for="result in results" :key="result.id">
                    <div @click="selectTarget(result)"
                         class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-navy-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="result.name"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="result.additional_info || ''"></div>
                    </div>
                </template>

                <!-- Sem resultados -->
                <div x-show="!loading && results.length === 0 && searchQuery.length > 0"
                     class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                    Nenhum resultado encontrado para "<span x-text="searchQuery"></span>"
                </div>

                <!-- Mensagem inicial -->
                <div x-show="!loading && results.length === 0 && searchQuery.length === 0"
                     class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                    Digite para pesquisar
                </div>
            </div>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="target_help_text"></p>
        <x-input-error :messages="$errors->get('target_id')" class="mt-1" />
    </div>

    <!-- Tipo de Regra -->
    <div>
        <x-input-label for="rule_type" value="Tipo de Regra *" />
        <x-ui.select name="rule_type" id="rule_type" class="mt-1" required
                     x-data="{}"
                     @change="updateRuleFields()">
            <option value="">Selecione...</option>
            <option value="fixed" @selected(old('rule_type', $logbookRule->rule_type ?? '') == 'fixed')>Valor Fixo</option>
            <option value="formula" @selected(old('rule_type', $logbookRule->rule_type ?? '') == 'formula')>Fórmula</option>
        </x-ui.select>
        <x-input-error :messages="$errors->get('rule_type')" class="mt-1" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="is_active" value="Status" />
        <div class="mt-2">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $logbookRule->is_active ?? true))
                       class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Regra ativa</span>
            </label>
        </div>
        <x-input-error :messages="$errors->get('is_active')" class="mt-1" />
    </div>

    <!-- Campo de Valor Fixo -->
    <div id="fixed_value_container" style="display: none;">
        <x-input-label for="fixed_value" value="Valor Fixo (km) *" />
        <x-text-input id="fixed_value" name="fixed_value" type="number" class="mt-1 block w-full"
                      :value="old('fixed_value', $logbookRule->fixed_value ?? '')"
                      placeholder="Ex: 500" min="1" />
        <x-input-error :messages="$errors->get('fixed_value')" class="mt-1" />
    </div>

    <!-- Campos de Fórmula -->
    <div id="formula_container" style="display: none;" class="md:col-span-2">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="formula_type" value="Tipo de Fórmula *" />
                <x-ui.select name="formula_type" id="formula_type" class="mt-1">
                    <option value="">Selecione...</option>
                    <option value="daily_average_plus_fixed" @selected(old('formula_type', $logbookRule->formula_type ?? '') == 'daily_average_plus_fixed')>Média Diária + Valor Fixo</option>
                    <option value="daily_average_plus_percentage" @selected(old('formula_type', $logbookRule->formula_type ?? '') == 'daily_average_plus_percentage')>Média Diária + Percentual</option>
                </x-ui.select>
                <x-input-error :messages="$errors->get('formula_type')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="formula_value" value="Valor da Fórmula *" />
                <x-text-input id="formula_value" name="formula_value" type="number" class="mt-1 block w-full"
                              :value="old('formula_value', $logbookRule->formula_value ?? '')"
                              placeholder="Ex: 100" min="1" />
                <x-input-error :messages="$errors->get('formula_value')" class="mt-1" />
            </div>
        </div>
    </div>
</div>

<!-- Descrição da Regra -->
<div class="pt-4">
    <x-input-label for="description" value="Descrição (Opcional)" />
    <textarea id="description" name="description" rows="3"
              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
              placeholder="Descreva o propósito desta regra...">{{ old('description', $logbookRule->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-1" />
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar estados dos campos
        updateTargetOptions();
        updateRuleFields();

        // Observar mudanças nos selects
        document.getElementById('target_type').addEventListener('change', updateTargetOptions);
        document.getElementById('rule_type').addEventListener('change', updateRuleFields);

        function updateTargetOptions() {
            const targetType = document.getElementById('target_type').value;
            const targetContainer = document.getElementById('target_specific_container');
            const targetHelpText = document.getElementById('target_help_text');

            if (targetType === 'global') {
                targetContainer.style.display = 'none';
                document.getElementById('target_id').removeAttribute('required');
            } else {
                targetContainer.style.display = 'block';
                document.getElementById('target_id').setAttribute('required', 'required');

                // Atualizar texto de ajuda
                let helpText = '';
                switch(targetType) {
                    case 'vehicle_category':
                        helpText = 'Digite o nome da categoria de veículo';
                        break;
                    case 'user':
                        helpText = 'Digite o nome ou email do usuário';
                        break;
                    case 'vehicle':
                        helpText = 'Digite o nome, placa ou marca do veículo';
                        break;
                }
                targetHelpText.textContent = helpText;
            }
        }

        function updateRuleFields() {
            const ruleType = document.getElementById('rule_type').value;
            const fixedContainer = document.getElementById('fixed_value_container');
            const formulaContainer = document.getElementById('formula_container');

            if (ruleType === 'fixed') {
                fixedContainer.style.display = 'block';
                formulaContainer.style.display = 'none';
                document.getElementById('fixed_value').setAttribute('required', 'required');
                document.getElementById('formula_type').removeAttribute('required');
                document.getElementById('formula_value').removeAttribute('required');
            } else if (ruleType === 'formula') {
                fixedContainer.style.display = 'none';
                formulaContainer.style.display = 'block';
                document.getElementById('fixed_value').removeAttribute('required');
                document.getElementById('formula_type').setAttribute('required', 'required');
                document.getElementById('formula_value').setAttribute('required', 'required');
            } else {
                fixedContainer.style.display = 'none';
                formulaContainer.style.display = 'none';
                document.getElementById('fixed_value').removeAttribute('required');
                document.getElementById('formula_type').removeAttribute('required');
                document.getElementById('formula_value').removeAttribute('required');
            }
        }

        // Preencher campos baseado nos dados existentes
        const currentTargetType = '{{ old("target_type", $logbookRule->target_type ?? "") }}';
        const currentRuleType = '{{ old("rule_type", $logbookRule->rule_type ?? "") }}';

        if (currentTargetType) {
            setTimeout(() => {
                document.getElementById('target_type').value = currentTargetType;
                updateTargetOptions();
            }, 100);
        }

        if (currentRuleType) {
            setTimeout(() => {
                document.getElementById('rule_type').value = currentRuleType;
                updateRuleFields();
            }, 100);
        }
    });

    function targetSearch() {
        return {
            searchQuery: '',
            selectedId: '{{ old("target_id", $logbookRule->target_id ?? "") }}',
            results: [],
            showDropdown: false,
            loading: false,

            init() {
                // Se já houver um target_id selecionado, carregar o nome
                if (this.selectedId) {
                    this.loadSelectedTarget();
                }
            },

            loadSelectedTarget() {
                const targetType = document.getElementById('target_type').value;
                if (!targetType || targetType === 'global') return;

                this.loading = true;
                let endpoint = '';
                switch(targetType) {
                    case 'vehicle_category':
                        endpoint = '/api/vehicle-categories/' + this.selectedId;
                        break;
                    case 'user':
                        endpoint = '/api/users/' + this.selectedId;
                        break;
                    case 'vehicle':
                        endpoint = '/api/vehicles/' + this.selectedId;
                        break;
                }

                if (endpoint) {
                    fetch(endpoint)
                        .then(response => {
                            if (!response.ok) throw new Error('Não encontrado');
                            return response.json();
                        })
                        .then(data => {
                            this.searchQuery = data.name || data.text || (data.brand + ' ' + data.model_year);
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Erro ao carregar alvo:', error);
                            this.loading = false;
                        });
                }
            },

            search() {
                const targetType = document.getElementById('target_type').value;
                if (!targetType || targetType === 'global' || !this.searchQuery.trim()) {
                    this.results = [];
                    return;
                }

                this.loading = true;

                let endpoint = '';
                switch(targetType) {
                    case 'vehicle_category':
                        endpoint = '{{ route("api.vehicle-categories.search") }}?q=' + encodeURIComponent(this.searchQuery);
                        break;
                    case 'user':
                        endpoint = '{{ route("api.users.search") }}?q=' + encodeURIComponent(this.searchQuery);
                        break;
                    case 'vehicle':
                        endpoint = '{{ route("api.vehicles.search") }}?q=' + encodeURIComponent(this.searchQuery);
                        break;
                }

                if (endpoint) {
                    fetch(endpoint)
                        .then(response => response.json())
                        .then(data => {
                            // Formatar os resultados baseado no tipo
                            this.results = data.map(item => {
                                let additional_info = '';
                                switch(targetType) {
                                    case 'user':
                                        additional_info = item.email || '';
                                        break;
                                    case 'vehicle':
                                        additional_info = `${item.plate} • ${item.brand} ${item.model_year}`;
                                        break;
                                    case 'vehicle_category':
                                        additional_info = '';
                                        break;
                                }
                                return {
                                    id: item.id,
                                    name: item.name || item.text || (item.brand ? `${item.brand} ${item.model_year}` : ''),
                                    additional_info: additional_info
                                };
                            });
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Erro na busca:', error);
                            this.loading = false;
                            this.results = [];
                        });
                }
            },

            selectTarget(target) {
                this.searchQuery = target.name;
                this.selectedId = target.id;
                this.showDropdown = false;
                this.results = [];
            },

            closeDropdown() {
                setTimeout(() => {
                    this.showDropdown = false;
                }, 200);
            }
        }
    }

    // Função para atualizar opções do target
    function updateTargetOptions() {
        const targetType = document.getElementById('target_type').value;
        const targetSelect = document.getElementById('target_id');

        console.log('Target type selecionado:', targetType);

        // Limpa opções existentes
        targetSelect.innerHTML = '<option value="">Selecione...</option>';

        if (targetType === 'global') {
            targetSelect.disabled = true;
            return;
        }

        targetSelect.disabled = false;

        // Faz requisição baseada no tipo selecionado
        let endpoint = '';

        switch(targetType) {
            case 'vehicle_category':
                endpoint = '/api/vehicle-categories';
                break;
            case 'vehicle':
                endpoint = '/api/vehicles'; // Agora usa a rota correta
                break;
            case 'user':
                endpoint = '/api/users';
                break;
        }

        if (endpoint) {
            console.log('Fazendo requisição para:', endpoint);

            fetch(endpoint, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    console.log('Status da resposta:', response.status, response.statusText);
                    if (!response.ok) {
                        throw new Error(`Erro na resposta da API: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);

                    if (Array.isArray(data)) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id;

                            // Para veículos, usa display_name que já tem o prefixo primeiro
                            if (targetType === 'vehicle') {
                                option.textContent = item.display_name || item.full_name || item.name;
                                option.setAttribute('data-prefix', item.prefix_id || '');
                            } else {
                                // Para categorias e usuários, mostra apenas o nome
                                option.textContent = item.name;
                            }
                            targetSelect.appendChild(option);
                        });

                        // Restaura seleção anterior se existir
                        const previousValue = targetSelect.getAttribute('data-previous-value');
                        if (previousValue && targetSelect.querySelector(`option[value="${previousValue}"]`)) {
                            targetSelect.value = previousValue;
                        }

                        console.log('Opções carregadas com sucesso');
                    } else {
                        console.error('Resposta da API não é um array:', data);
                        showError(targetSelect, 'Erro: Formato de dados inválido');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar opções:', error);
                    showError(targetSelect, 'Erro ao carregar opções: ' + error.message);
                });
        }
    }

    // Função para mostrar erro no select
    function showError(selectElement, message) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = message;
        option.disabled = true;
        selectElement.appendChild(option);
    }

    // Função para atualizar campos da regra
    function updateRuleFields() {
        const ruleType = document.getElementById('rule_type').value;
        const fixedValueField = document.getElementById('fixed_value_field');
        const formulaFields = document.getElementById('formula_fields');
        const formulaTypeField = document.getElementById('formula_type_field');
        const formulaValueField = document.getElementById('formula_value_field');

        console.log('Tipo de regra selecionado:', ruleType);

        if (ruleType === 'fixed') {
            // Mostra apenas campo de valor fixo
            if (fixedValueField) fixedValueField.style.display = 'block';
            if (formulaFields) formulaFields.style.display = 'none';
        } else if (ruleType === 'formula') {
            // Mostra campos de fórmula
            if (fixedValueField) fixedValueField.style.display = 'none';
            if (formulaFields) formulaFields.style.display = 'block';
        }
    }

    // Função para carregar dados no editar
    function loadEditData() {
        // Se estiver na página de edição, carrega os dados
        const descriptionField = document.getElementById('description');
        const targetTypeField = document.getElementById('target_type');
        const targetIdField = document.getElementById('target_id');

        // Se há um target_id selecionado, atualiza as opções
        if (targetIdField && targetIdField.value) {
            targetTypeField.setAttribute('data-previous-value', targetIdField.value);
            // Força atualização após um breve delay para garantir que o DOM está pronto
            setTimeout(updateTargetOptions, 100);
        }

        console.log('Dados de edição carregados');
    }

    // Inicialização quando o documento estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Documento carregado, inicializando funções...');

        // Salva valores atuais antes de qualquer mudança
        const targetIdField = document.getElementById('target_id');
        if (targetIdField && targetIdField.value) {
            targetIdField.setAttribute('data-previous-value', targetIdField.value);
        }

        // Inicializa campos da regra
        updateRuleFields();

        // Inicializa opções do target
        updateTargetOptions();

        // Carrega dados se for edição
        loadEditData();

        // Adiciona event listeners
        const targetTypeSelect = document.getElementById('target_type');
        if (targetTypeSelect) {
            targetTypeSelect.addEventListener('change', function() {
                // Salva o valor atual antes de mudar
                const targetIdField = document.getElementById('target_id');
                if (targetIdField && targetIdField.value) {
                    targetIdField.setAttribute('data-previous-value', targetIdField.value);
                }
                updateTargetOptions();
            });
        }

        const ruleTypeSelect = document.getElementById('rule_type');
        if (ruleTypeSelect) {
            ruleTypeSelect.addEventListener('change', updateRuleFields);
        }

        console.log('Funções inicializadas com sucesso');
    });

    // Torna as funções globais para o Alpine.js
    window.updateTargetOptions = updateTargetOptions;
    window.updateRuleFields = updateRuleFields;
    window.loadEditData = loadEditData;

    // Função para buscar veículos por prefixo (se necessário)
    function searchVehiclesByPrefix(prefix) {
        fetch(`/api/vehicles?search_by=prefix&prefix=${encodeURIComponent(prefix)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta da API');
                }
                return response.json();
            })
            .then(data => {
                console.log('Veículos encontrados:', data);
                // Processar os resultados da busca
            })
            .catch(error => {
                console.error('Erro na busca:', error);
            });
    }

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        // Salva o valor atual antes de atualizar as opções
        const targetSelect = document.getElementById('target_id');
        if (targetSelect) {
            targetSelect.setAttribute('data-previous-value', targetSelect.value);
        }

        updateTargetOptions();

        const targetTypeSelect = document.getElementById('target_type');
        if (targetTypeSelect) {
            targetTypeSelect.addEventListener('change', function() {
                // Salva o valor atual antes de mudar
                const targetSelect = document.getElementById('target_id');
                if (targetSelect) {
                    targetSelect.setAttribute('data-previous-value', targetSelect.value);
                }
                updateTargetOptions();
            });
        }
    });

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        updateTargetOptions();

        const targetTypeSelect = document.getElementById('target_type');
        if (targetTypeSelect) {
            targetTypeSelect.addEventListener('change', updateTargetOptions);
        }
    });

    // Inicializa quando o documento estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        updateTargetOptions();
    });
    // Função para verificar regra duplicada em tempo real
    function checkDuplicateRule(targetType, targetId, currentRuleId = null) {
        if (!targetType) return Promise.resolve(true);

        // Se for global, targetId é null
        const checkTargetId = targetType === 'global' ? null : targetId;

        return fetch('/api/check-duplicate-rule', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                target_type: targetType,
                target_id: checkTargetId,
                current_rule_id: currentRuleId
            })
        })
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta da API');
                return response.json();
            })
            .then(data => {
                return data.valid;
            })
            .catch(error => {
                console.error('Erro ao verificar regra duplicada:', error);
                return true; // Em caso de erro, permite prosseguir
            });
    }

    // Função para mostrar/ocultar mensagem de erro
    function showDuplicateError(message) {
        // Remove mensagem anterior se existir
        hideDuplicateError();

        const errorDiv = document.createElement('div');
        errorDiv.id = 'duplicate-rule-error';
        errorDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm';
        errorDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>${message}</span>
        </div>
    `;

        // Insere após o campo target_type
        const targetTypeField = document.getElementById('target_type');
        targetTypeField.parentNode.appendChild(errorDiv);
    }

    function hideDuplicateError() {
        const existingError = document.getElementById('duplicate-rule-error');
        if (existingError) {
            existingError.remove();
        }
    }

    // Validação em tempo real quando o target_type muda
    document.addEventListener('DOMContentLoaded', function() {
        const targetTypeSelect = document.getElementById('target_type');
        const targetIdInput = document.getElementById('target_id');
        const currentRuleId = '{{ $logbookRule->id ?? null }}';

        if (targetTypeSelect) {
            targetTypeSelect.addEventListener('change', function() {
                const targetType = this.value;

                // Esconde mensagem de erro anterior
                hideDuplicateError();

                // Se for global, verifica imediatamente
                if (targetType === 'global') {
                    checkDuplicateRule(targetType, null, currentRuleId).then(isValid => {
                        if (!isValid) {
                            showDuplicateError('Já existe uma regra global ativa no sistema. Só é permitida uma regra global por vez.');
                        }
                    });
                }
            });
        }

        // Validação quando um target específico é selecionado
        if (targetIdInput) {
            // Usando MutationObserver para detectar mudanças no target_id
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        const targetType = document.getElementById('target_type').value;
                        const targetId = targetIdInput.value;

                        if (targetType && targetType !== 'global' && targetId) {
                            checkDuplicateRule(targetType, targetId, currentRuleId).then(isValid => {
                                if (!isValid) {
                                    const targetName = getTargetName(targetType, targetId);
                                    showDuplicateError(`Já existe uma regra ativa para ${targetName}. Cada alvo só pode ter uma regra ativa.`);
                                } else {
                                    hideDuplicateError();
                                }
                            });
                        }
                    }
                });
            });

            observer.observe(targetIdInput, { attributes: true });
        }

        // Também valida no blur do campo de busca
        const targetSearchInput = document.getElementById('target_search');
        if (targetSearchInput) {
            targetSearchInput.addEventListener('blur', function() {
                setTimeout(() => {
                    const targetType = document.getElementById('target_type').value;
                    const targetId = document.getElementById('target_id').value;

                    if (targetType && targetType !== 'global' && targetId) {
                        checkDuplicateRule(targetType, targetId, currentRuleId).then(isValid => {
                            if (!isValid) {
                                const targetName = getTargetName(targetType, targetId);
                                showDuplicateError(`Já existe uma regra ativa para ${targetName}. Cada alvo só pode ter uma regra ativa.`);
                            }
                        });
                    }
                }, 200);
            });
        }
    });

    // Função auxiliar para obter o nome do target (simulação)
    function getTargetName(targetType, targetId) {
        // Esta função precisaria ser implementada baseada nos dados carregados
        // Por enquanto, retorna um texto genérico
        switch(targetType) {
            case 'vehicle_category': return 'esta categoria de veículo';
            case 'user': return 'este usuário';
            case 'vehicle': return 'este veículo';
            default: return 'este alvo';
        }
    }

    // Usar no evento change do target
    document.getElementById('target_type').addEventListener('change', function() {
        const targetType = this.value;
        const targetId = document.getElementById('target_id').value;
        const currentRuleId = '{{ $logbookRule->id ?? null }}';

        if (targetType && targetId) {
            checkDuplicateRule(targetType, targetId, currentRuleId).then(isValid => {
                if (!isValid) {
                    alert('Já existe uma regra ativa para este alvo!');
                    document.getElementById('target_id').value = '';
                }
            });
        }
    });
</script>

<div class="flex items-center gap-3 pt-6">
    <x-primary-button icon="save" compact>Salvar Regra</x-primary-button>
    <a href="{{ route('logbook-rules.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
</div>
