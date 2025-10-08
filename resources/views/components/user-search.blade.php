{{-- Componente de Pesquisa Inteligente de Usuários --}}
@props([
    'name' => 'user_id',
    'label' => 'Usuário *',
    'required' => true,
    'selectedId' => old('user_id', $selectedUserId ?? ''),
    'selectedName' => old('user_name', $selectedUserName ?? ''),
    'placeholder' => 'Digite o nome ou CPF do usuário...',
    'roles' => null, // Array de roles para filtrar: ['driver', 'sector_manager']
])

<div x-data="userSearch('{{ $selectedId }}', '{{ $selectedName }}', {{ json_encode($roles) }})">
    <x-input-label :for="$name . '_search'" :value="$label" />
    <div class="relative">
        <input
            type="text"
            :id="'{{ $name }}_search'"
            x-model="searchQuery"
            @input="search()"
            @focus="showDropdown = true"
            @click.away="closeDropdown()"
            :placeholder="placeholder"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
            autocomplete="off"
            {{ $required ? 'required' : '' }}
        />
        <input type="hidden" name="{{ $name }}" x-model="selectedId" {{ $required ? 'required' : '' }}>

        <!-- Dropdown de resultados -->
        <div x-show="showDropdown && (results.length > 0 || searchQuery.length > 0)"
             x-cloak
             x-transition
             class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto">

            <!-- Loading -->
            <div x-show="loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Pesquisando...
                </div>
            </div>

            <!-- Resultados -->
            <template x-for="result in results" :key="result.id">
                <div @click="selectUser(result)"
                     class="px-4 py-3 cursor-pointer hover:bg-primary-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="result.name"></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="'CPF: ' + result.cpf"></span>
                                <span class="mx-2">•</span>
                                <span x-text="result.role"></span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="result.secretariat"></div>
                        </div>
                        <div class="ml-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :class="result.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                  x-text="result.status === 'active' ? 'Ativo' : 'Inativo'">
                            </span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Sem resultados -->
            <div x-show="!loading && results.length === 0 && searchQuery.length === 0"
                 class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                Digite o nome ou CPF para pesquisar usuários
            </div>

            <div x-show="!loading && results.length === 0 && searchQuery.length > 0"
                 class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                Nenhum usuário encontrado
            </div>
        </div>
    </div>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
        Digite o nome ou CPF do usuário para pesquisar
    </p>
    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div>

<script>
function userSearch(initialId, initialName, filterRoles = null) {
    return {
        searchQuery: initialName,
        selectedId: initialId,
        results: [],
        showDropdown: false,
        loading: false,
        searchTimeout: null,
        placeholder: '{{ $placeholder }}',

        search() {
            clearTimeout(this.searchTimeout);

            if (this.searchQuery.length === 0) {
                this.results = [];
                this.selectedId = '';
                return;
            }

            if (this.searchQuery.length < 2) {
                return; // Esperar pelo menos 2 caracteres
            }

            this.searchTimeout = setTimeout(() => {
                this.loading = true;

                let url = `/api/users/search?q=${encodeURIComponent(this.searchQuery)}`;
                if (filterRoles) {
                    url += `&roles=${encodeURIComponent(JSON.stringify(filterRoles))}`;
                }

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        this.results = data;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Erro ao buscar usuários:', error);
                        this.loading = false;
                    });
            }, 300);
        },

        selectUser(user) {
            this.searchQuery = user.name;
            this.selectedId = user.id;
            this.showDropdown = false;
        },

        closeDropdown() {
            setTimeout(() => {
                this.showDropdown = false;
            }, 200);
        }
    }
}
</script>

