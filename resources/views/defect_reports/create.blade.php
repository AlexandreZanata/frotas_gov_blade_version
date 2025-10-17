<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Comunicar Novo Defeito" subtitle="Preencha o formulário para reportar um ou mais defeitos" icon="exclamation-triangle" />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('defect-reports.store') }}" method="POST" x-data="{ answers: [{ item_id: '', severity: 'low', notes: '' }] }">
            @csrf
            <div class="space-y-6">
                <!-- Seleção de Veículo -->
                <div>
                    <x-input-label for="vehicle_id" value="Veículo" />
                    <x-ui.select name="vehicle_id" id="vehicle_id" required>
                        <option value="">Selecione um veículo...</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                {{ $vehicle->prefix->name ?? '' }} - {{ $vehicle->name }} ({{ $vehicle->plate }})
                            </option>
                        @endforeach
                    </x-ui.select>
                    <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
                </div>

                <!-- Itens do Defeito -->
                <div>
                    <h3 class="font-semibold mb-2 text-gray-800 dark:text-navy-100">Itens Defeituosos</h3>
                    <div class="space-y-4">
                        <template x-for="(answer, index) in answers" :key="index">
                            <div class="p-4 border dark:border-navy-600 rounded-lg space-y-3 bg-gray-50 dark:bg-navy-800/50">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label ::for="'item_id_'+index" value="Item do Defeito" />
                                        {{-- CORREÇÃO AQUI: Adicionado 'name' estático --}}
                                        <x-ui.select name="answers[0][item_id]" ::name="'answers['+index+'][item_id]'" ::id="'item_id_'+index" required>
                                            <option value="">Selecione um item...</option>
                                            @foreach($defectItems as $category => $items)
                                                <optgroup label="{{ $category }}">
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </x-ui.select>
                                    </div>
                                    <div>
                                        <x-input-label ::for="'severity_'+index" value="Gravidade" />
                                        {{-- CORREÇÃO AQUI: Adicionado 'name' estático --}}
                                        <x-ui.select name="answers[0][severity]" ::name="'answers['+index+'][severity]'" ::id="'severity_'+index" required>
                                            <option value="low">Baixa</option>
                                            <option value="medium">Média</option>
                                            <option value="high">Alta</option>
                                        </x-ui.select>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label ::for="'notes_'+index" value="Observações sobre o item (opcional)" />
                                    <x-text-input ::name="'answers['+index+'][notes]'" ::id="'notes_'+index" class="w-full" placeholder="Ex: Barulho ao frear, luz piscando..." />
                                </div>
                                <button type="button" @click="answers.splice(index, 1)" class="text-red-500 text-sm hover:underline" x-show="answers.length > 1">Remover Item</button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="answers.push({ item_id: '', severity: 'low', notes: '' })" class="mt-4 text-sm inline-flex items-center gap-1 text-primary-600 hover:underline">
                        <x-icon name="plus" class="w-3 h-3" />
                        Adicionar outro item
                    </button>
                    <x-input-error :messages="$errors->get('answers')" class="mt-1" />
                </div>

                <!-- Observações Gerais -->
                <div>
                    <x-input-label for="notes" value="Observações Gerais (opcional)" />
                    <x-ui.textarea id="notes" name="notes" class="w-full" rows="4">{{ old('notes') }}</x-ui.textarea>
                </div>

                <div class="flex items-center gap-3 pt-3">
                    <x-primary-button icon="paper-airplane" compact>Enviar Relatório</x-primary-button>
                    <a href="{{ route('defect-reports.index') }}" class="text-sm text-gray-600 dark:text-navy-300 hover:text-gray-800 dark:hover:text-navy-100">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>

