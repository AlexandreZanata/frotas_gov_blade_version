@props([
    'name' => 'secretariat_ids',
    'label' => 'Secretarias',
    'required' => false,
    'selectedIds' => [],
    'placeholder' => 'Pesquise por nome da secretaria...'
])

<div x-data="secretariatSearchComponent({{ json_encode($selectedIds) }})" x-init="init()">
    <x-input-label :for="$name" :value="$label" />

    <!-- Campo de Pesquisa -->
    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-icon name="building" class="w-5 h-5 text-gray-400" />
        </div>
        <input
            type="text"
            x-model="searchQuery"
            @input.debounce.300ms="searchSecretariats()"
            @focus="showResults = true"
            placeholder="{{ $placeholder }}"
            class="pl-10 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
        />

        <!-- Loading Indicator -->
        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Resultados da Pesquisa -->
    <div
        x-show="showResults && searchQuery.length > 0"
        @click.away="showResults = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto"
        x-cloak
    >
        <template x-if="filteredSecretariats.length === 0 && !loading">
            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                Nenhuma secretaria encontrada
            </div>
        </template>

        <template x-for="secretariat in filteredSecretariats" :key="secretariat.id">
            <button
                type="button"
                @click="toggleSecretariat(secretariat)"
                class="w-full text-left px-4 py-3 hover:bg-primary-50 dark:hover:bg-gray-700 transition flex items-center justify-between"
                :class="{ 'bg-primary-50 dark:bg-gray-700': isSelected(secretariat.id) }"
            >
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <x-icon name="building" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="secretariat.name"></span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-6">
                        <span x-text="secretariat.vehicle_count"></span> veículos cadastrados
                    </div>
                </div>
                <x-icon
                    name="check"
                    class="w-5 h-5 text-primary-600 dark:text-primary-400"
                    x-show="isSelected(secretariat.id)"
                />
            </button>
        </template>
    </div>

    <!-- Secretarias Selecionadas -->
    <div class="mt-3 space-y-2" x-show="selectedSecretariats.length > 0">
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Secretarias Selecionadas (<span x-text="selectedSecretariats.length"></span>)
        </div>
        <div class="border border-gray-300 dark:border-gray-700 rounded-md p-3 bg-gray-50 dark:bg-gray-900 max-h-48 overflow-y-auto">
            <template x-for="secretariat in selectedSecretariats" :key="secretariat.id">
                <div class="flex items-center justify-between py-2 px-3 bg-white dark:bg-gray-800 rounded mb-2 last:mb-0">
                    <div class="flex-1 flex items-center gap-2">
                        <x-icon name="building" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                        <div>
                            <span class="font-medium text-gray-900 dark:text-gray-100" x-text="secretariat.name"></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                (<span x-text="secretariat.vehicle_count"></span> veículos)
                            </span>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="removeSecretariat(secretariat.id)"
                        class="ml-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    >
                        <x-icon name="close" class="w-5 h-5" />
                    </button>
                    <!-- Hidden input para submeter o formulário -->
                    <input type="hidden" :name="'{{ $name }}[]'" :value="secretariat.id" />
                </div>
            </template>
        </div>
    </div>

    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
        Pesquise e selecione as secretarias. O usuário terá acesso a todos os veículos das secretarias selecionadas.
    </p>
    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>

<script>
function secretariatSearchComponent(initialSelectedIds) {
    return {
        searchQuery: '',
        allSecretariats: [],
        filteredSecretariats: [],
        selectedSecretariats: [],
        showResults: false,
        loading: false,

        init() {
            this.loadAllSecretariats();
        },

        async loadAllSecretariats() {
            this.loading = true;
            try {
                const response = await fetch('/api/secretariats/search');
                const data = await response.json();
                this.allSecretariats = data;

                // Carregar secretarias pré-selecionadas
                if (initialSelectedIds.length > 0) {
                    this.selectedSecretariats = this.allSecretariats.filter(s =>
                        initialSelectedIds.includes(s.id)
                    );
                }
            } catch (error) {
                console.error('Erro ao carregar secretarias:', error);
            } finally {
                this.loading = false;
            }
        },

        searchSecretariats() {
            if (this.searchQuery.trim().length === 0) {
                this.filteredSecretariats = [];
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.filteredSecretariats = this.allSecretariats.filter(secretariat => {
                const name = (secretariat.name || '').toLowerCase();
                return name.includes(query);
            }).slice(0, 10); // Limitar a 10 resultados
        },

        toggleSecretariat(secretariat) {
            if (this.isSelected(secretariat.id)) {
                this.removeSecretariat(secretariat.id);
            } else {
                this.selectedSecretariats.push(secretariat);
            }
            this.searchQuery = '';
            this.filteredSecretariats = [];
            this.showResults = false;
        },

        removeSecretariat(secretariatId) {
            this.selectedSecretariats = this.selectedSecretariats.filter(s => s.id !== secretariatId);
        },

        isSelected(secretariatId) {
            return this.selectedSecretariats.some(s => s.id === secretariatId);
        }
    };
}
</script>

