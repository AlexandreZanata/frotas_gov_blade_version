<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Checklist do Veículo"
            subtitle="Preencha o checklist antes de iniciar a corrida"
            hide-title-mobile
            icon="clipboard-check"
        />
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <x-ui.flash />

            <!-- Informações do Veículo -->
            <x-ui.card title="Veículo Selecionado">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Prefixo</p>
                        <p class="font-semibold">{{ $vehicle->prefix->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nome</p>
                        <p class="font-semibold">{{ $vehicle->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Placa</p>
                        <p class="font-semibold">{{ $vehicle->plate }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Categoria</p>
                        <p class="font-semibold">{{ $vehicle->category->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Formulário de Checklist -->
            <x-ui.card title="Checklist de Segurança" subtitle="Marque o estado de cada item">
                <form action="{{ route('logbook.store-checklist-form') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="space-y-4">
                        @foreach($checklistItems as $item)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $item->name }}</h4>
                                        @if($item->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Radio Buttons -->
                                <div class="flex gap-4 mb-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input
                                            type="radio"
                                            name="checklist[{{ $item->id }}][status]"
                                            value="ok"
                                            class="text-green-600 focus:ring-green-500"
                                            @checked(old("checklist.{$item->id}.status", $lastChecklistState[$item->id]['status'] ?? null) === 'ok')
                                            required
                                        >
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium text-green-600">OK</span>
                                        </span>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input
                                            type="radio"
                                            name="checklist[{{ $item->id }}][status]"
                                            value="problem"
                                            class="text-red-600 focus:ring-red-500"
                                            @checked(old("checklist.{$item->id}.status", $lastChecklistState[$item->id]['status'] ?? null) === 'problem')
                                        >
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium text-red-600">Problema</span>
                                        </span>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input
                                            type="radio"
                                            name="checklist[{{ $item->id }}][status]"
                                            value="not_applicable"
                                            class="text-gray-600 focus:ring-gray-500"
                                            @checked(old("checklist.{$item->id}.status", $lastChecklistState[$item->id]['status'] ?? null) === 'not_applicable')
                                        >
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium text-gray-600">N/A</span>
                                        </span>
                                    </label>
                                </div>

                                <!-- Notas -->
                                <div>
                                    <x-text-input
                                        name="checklist[{{ $item->id }}][notes]"
                                        type="text"
                                        class="block w-full text-sm"
                                        placeholder="Observações (opcional)"
                                        :value="old('checklist.'.$item->id.'.notes', $lastChecklistState[$item->id]['notes'] ?? '')"
                                    />
                                </div>

                                @error("checklist.{$item->id}.status")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                            placeholder="Adicione observações gerais sobre o veículo..."
                        >{{ old('general_notes') }}</textarea>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-primary-button icon="check" compact>Salvar e Continuar</x-primary-button>
                        <a href="{{ route('logbook.vehicle-select') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">
                            Voltar à Seleção
                        </a>
                    </div>
                </form>
            </x-ui.card>

            <!-- Informação -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-blue-900 dark:text-blue-100">Importante</h4>
                        <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                            O checklist é obrigatório antes de iniciar a corrida. A corrida será criada automaticamente após o preenchimento do checklist.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

