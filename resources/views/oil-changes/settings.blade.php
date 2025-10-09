<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Configurações de Troca de Óleo"
            subtitle="Defina intervalos padrão por categoria de veículo"
            hide-title-mobile
            icon="settings"
        />
    </x-slot>

    <x-slot name="pageActions">
        <a href="{{ route('oil-changes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white text-sm font-medium shadow transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Voltar</span>
        </a>
    </x-slot>

    <!-- Informações -->
    <x-ui.alert-card title="Configurações de Intervalos" variant="info" icon="alert-circle">
        <p>Configure os intervalos padrão de troca de óleo para cada categoria de veículo. Estes valores serão sugeridos automaticamente ao registrar uma nova troca.</p>
    </x-ui.alert-card>

    <!-- Formulário de Configurações -->
    <x-ui.card title="Intervalos por Categoria" subtitle="Defina os intervalos de quilometragem, tempo e quantidade de óleo">
        <form method="POST" action="{{ route('oil-changes.settings.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-6">
                @foreach($categories as $category)
                    @php
                        $setting = $settings->firstWhere('vehicle_category_id', $category->id);
                    @endphp

                    <div class="bg-gray-50 dark:bg-navy-900 rounded-lg p-5 border border-gray-200 dark:border-navy-700">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <x-icon name="car" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-navy-400">{{ $category->vehicles_count ?? 0 }} veículos nesta categoria</p>
                            </div>
                        </div>

                        <input type="hidden" name="settings[{{ $category->id }}][vehicle_category_id]" value="{{ $category->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="km_interval_{{ $category->id }}" value="Intervalo de KM *" />
                                <input
                                    type="number"
                                    name="settings[{{ $category->id }}][km_interval]"
                                    id="km_interval_{{ $category->id }}"
                                    value="{{ $setting->km_interval ?? 10000 }}"
                                    required
                                    min="1000"
                                    step="1000"
                                    placeholder="Ex: 10000"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">Quilômetros entre trocas</p>
                            </div>

                            <div>
                                <x-input-label for="days_interval_{{ $category->id }}" value="Intervalo de Dias *" />
                                <input
                                    type="number"
                                    name="settings[{{ $category->id }}][days_interval]"
                                    id="days_interval_{{ $category->id }}"
                                    value="{{ $setting->days_interval ?? 180 }}"
                                    required
                                    min="30"
                                    step="30"
                                    placeholder="Ex: 180"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">Dias entre trocas (6 meses)</p>
                            </div>

                            <div>
                                <x-input-label for="default_liters_{{ $category->id }}" value="Litros Padrão" />
                                <input
                                    type="number"
                                    name="settings[{{ $category->id }}][default_liters]"
                                    id="default_liters_{{ $category->id }}"
                                    value="{{ $setting->default_liters ?? '' }}"
                                    step="0.5"
                                    min="0"
                                    placeholder="Ex: 4.5"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-500 dark:text-navy-400">Litros sugeridos (opcional)</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-navy-700">
                <a href="{{ route('oil-changes.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-navy-700 dark:hover:bg-navy-600 text-gray-700 dark:text-white rounded-md font-medium transition">
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md font-medium transition inline-flex items-center gap-2">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Salvar Configurações</span>
                </button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>

