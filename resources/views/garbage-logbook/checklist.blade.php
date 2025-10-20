<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Checklist do Veículo"
            subtitle="Preencha o checklist antes de iniciar a coleta"
            hide-title-mobile
            icon="clipboard-check"
        />
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Informações do Veículo -->
            <x-ui.card title="Veículo Selecionado">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-navy-300">Prefixo</p>
                        <p class="font-semibold text-gray-900 dark:text-navy-50">{{ $vehicle->prefix->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-navy-300">Nome</p>
                        <p class="font-semibold text-gray-900 dark:text-navy-50">{{ $vehicle->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-navy-300">Placa</p>
                        <p class="font-semibold text-gray-900 dark:text-navy-50">{{ $vehicle->plate }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-navy-300">Categoria</p>
                        <p class="font-semibold text-gray-900 dark:text-navy-50">{{ $vehicle->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Formulário de Checklist -->
            <x-ui.card title="Checklist de Segurança" subtitle="Verifique cada item antes de iniciar a coleta">
                <form action="{{ route('garbage-logbook.store-checklist-form') }}" method="POST" class="space-y-6" x-data="checklistForm()">
                    @csrf

                    <div class="space-y-4">
                        @foreach($checklistItems as $item)
                            <div
                                x-data="{
                                    status: '{{ old('checklist.'.$item->id.'.status', $lastChecklistState[$item->id]['status'] ?? '') }}',
                                    notes: '{{ old('checklist.'.$item->id.'.notes', $lastChecklistState[$item->id]['notes'] ?? '') }}',
                                    showNotes: {{ old('checklist.'.$item->id.'.status', $lastChecklistState[$item->id]['status'] ?? '') === 'problem' ? 'true' : 'false' }}
                                }"
                                class="rounded-lg border transition-colors"
                                :class="{
                                    'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20': status === 'ok',
                                    'border-yellow-300 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/20': status === 'attention',
                                    'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20': status === 'problem',
                                    'border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800': !status
                                }">

                                <div class="p-4 space-y-4">
                                    <!-- Item Header -->
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-navy-50 text-lg">{{ $item->name }}</h4>
                                            @if($item->description)
                                                <p class="text-sm text-gray-600 dark:text-navy-300 mt-1">{{ $item->description }}</p>
                                            @endif
                                        </div>

                                        <!-- Status Badge (quando selecionado) -->
                                        <div x-show="status" x-transition>
                                            <span
                                                x-show="status === 'ok'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                OK
                                            </span>
                                            <span
                                                x-show="status === 'attention'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Atenção
                                            </span>
                                            <span
                                                x-show="status === 'problem'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Problema
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Status Buttons -->
                                    <div class="grid grid-cols-3 gap-3">
                                        <!-- OK - Verde -->
                                        <button
                                            type="button"
                                            @click="status = 'ok'; showNotes = false; notes = ''"
                                            :class="status === 'ok' ? 'ring-2 ring-green-500 bg-green-100 dark:bg-green-900/40 scale-105' : 'bg-white dark:bg-navy-700 hover:bg-gray-50 dark:hover:bg-navy-600'"
                                            class="flex flex-col items-center gap-2 p-4 rounded-lg transition-all shadow-sm hover:shadow-md"
                                        >
                                            <div
                                                class="rounded-full p-3 transition-colors"
                                                :class="status === 'ok' ? 'bg-green-500' : 'bg-gray-300 dark:bg-navy-500'"
                                            >
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span
                                                class="text-sm font-semibold"
                                                :class="status === 'ok' ? 'text-green-700 dark:text-green-300' : 'text-gray-600 dark:text-navy-300'"
                                            >
                                                OK
                                            </span>
                                        </button>

                                        <!-- Atenção - Amarelo -->
                                        <button
                                            type="button"
                                            @click="status = 'attention'; showNotes = false; notes = ''"
                                            :class="status === 'attention' ? 'ring-2 ring-yellow-500 bg-yellow-100 dark:bg-yellow-900/40 scale-105' : 'bg-white dark:bg-navy-700 hover:bg-gray-50 dark:hover:bg-navy-600'"
                                            class="flex flex-col items-center gap-2 p-4 rounded-lg transition-all shadow-sm hover:shadow-md"
                                        >
                                            <div
                                                class="rounded-full p-3 transition-colors"
                                                :class="status === 'attention' ? 'bg-yellow-500' : 'bg-gray-300 dark:bg-navy-500'"
                                            >
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span
                                                class="text-sm font-semibold"
                                                :class="status === 'attention' ? 'text-yellow-700 dark:text-yellow-300' : 'text-gray-600 dark:text-navy-300'"
                                            >
                                                Atenção
                                            </span>
                                        </button>

                                        <!-- Problema - Vermelho -->
                                        <button
                                            type="button"
                                            @click="status = 'problem'; showNotes = true"
                                            :class="status === 'problem' ? 'ring-2 ring-red-500 bg-red-100 dark:bg-red-900/40 scale-105' : 'bg-white dark:bg-navy-700 hover:bg-gray-50 dark:hover:bg-navy-600'"
                                            class="flex flex-col items-center gap-2 p-4 rounded-lg transition-all shadow-sm hover:shadow-md"
                                        >
                                            <div
                                                class="rounded-full p-3 transition-colors"
                                                :class="status === 'problem' ? 'bg-red-500' : 'bg-gray-300 dark:bg-navy-500'"
                                            >
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span
                                                class="text-sm font-semibold"
                                                :class="status === 'problem' ? 'text-red-700 dark:text-red-300' : 'text-gray-600 dark:text-navy-300'"
                                            >
                                                Problema
                                            </span>
                                        </button>
                                    </div>

                                    <!-- Campo de Notas (obrigatório quando "Problema") -->
                                    <div x-show="showNotes" x-transition class="space-y-2">
                                        <x-input-label :for="'notes_'.$item->id" value="Descreva o problema *" />
                                        <textarea
                                            :name="'checklist[{{ $item->id }}][notes]'"
                                            :id="'notes_{{ $item->id }}'"
                                            x-model="notes"
                                            rows="3"
                                            class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-red-500 dark:focus:border-red-500 focus:ring-red-500 shadow-sm"
                                            placeholder="Descreva detalhadamente o problema encontrado..."
                                            :required="status === 'problem'"
                                        ></textarea>
                                        <p class="text-xs text-red-600 dark:text-red-400">
                                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Esta descrição será enviada aos gestores para providências.
                                        </p>
                                    </div>

                                    <!-- Hidden inputs -->
                                    <input type="hidden" :name="'checklist[{{ $item->id }}][status]'" x-model="status" required>

                                    @error("checklist.{$item->id}.status")
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                    @error("checklist.{$item->id}.notes")
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Observações Gerais -->
                    <div>
                        <x-input-label for="general_notes" value="Observações Gerais (Opcional)" />
                        <textarea
                            id="general_notes"
                            name="general_notes"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-navy-700 dark:bg-navy-800 dark:text-navy-100 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                            placeholder="Adicione observações gerais sobre o veículo ou a inspeção..."
                        >{{ old('general_notes', '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Use este campo para adicionar qualquer observação relevante sobre o veículo ou a inspeção
                        </p>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                        <a href="{{ route('garbage-logbook.vehicle-select') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:text-gray-900 dark:hover:text-navy-50 hover:underline">
                            ← Voltar à Seleção
                        </a>
                        <x-primary-button icon="check" compact @click="$dispatch('submit-form')">
                            Salvar e Iniciar Coleta
                        </x-primary-button>
                    </div>
                </form>
            </x-ui.card>

            <!-- Informações Importantes -->
            <div class="grid md:grid-cols-2 gap-4">
                <!-- Info sobre o fluxo -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-blue-900 dark:text-blue-100">Como funciona</h4>
                            <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                                Ao salvar o checklist, a coleta será criada automaticamente e você poderá iniciar sua rota.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info sobre problemas -->
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-amber-900 dark:text-amber-100">Problemas detectados</h4>
                            <p class="text-sm text-amber-800 dark:text-amber-200 mt-1">
                                Itens marcados como "Problema" serão notificados aos gestores para providências imediatas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function checklistForm() {
                return {
                    init() {
                        // Validação antes de submeter
                        this.$el.addEventListener('submit', (e) => {
                            const allItems = this.$el.querySelectorAll('[name^="checklist["][name$="][status]"]');
                            let hasError = false;

                            allItems.forEach(input => {
                                if (!input.value) {
                                    hasError = true;
                                }
                            });

                            if (hasError) {
                                e.preventDefault();
                                alert('Por favor, selecione um status para todos os itens do checklist.');
                                return false;
                            }
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
