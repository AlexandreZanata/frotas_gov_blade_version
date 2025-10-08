@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="Nome" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $vehicle->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="brand" value="Marca" />
        <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full" :value="old('brand', $vehicle->brand ?? '')" required />
        <x-input-error :messages="$errors->get('brand')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="model_year" value="Ano / Modelo" />
        <x-text-input id="model_year" name="model_year" type="text" class="mt-1 block w-full" :value="old('model_year', $vehicle->model_year ?? '')" required />
        <x-input-error :messages="$errors->get('model_year')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="plate" value="Placa" />
        <x-input-plate id="plate" name="plate" :value="old('plate', $vehicle->plate ?? '')" class="mt-1 block w-full" required />
        <x-input-error :messages="$errors->get('plate')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="fuel_tank_capacity" value="Capacidade Tanque (L)" />
        <x-text-input id="fuel_tank_capacity" name="fuel_tank_capacity" type="number" class="mt-1 block w-full" :value="old('fuel_tank_capacity', $vehicle->fuel_tank_capacity ?? '')" required />
        <x-input-error :messages="$errors->get('fuel_tank_capacity')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="fuel_type_id" value="Tipo de Combustível" />
        <x-ui.select name="fuel_type_id" id="fuel_type_id" class="mt-1" required>
            <option value="">Selecione...</option>
            @foreach(($fuelTypes ?? []) as $ft)
                <option value="{{ $ft->id }}" @selected(old('fuel_type_id', $vehicle->fuel_type_id ?? '') == $ft->id)>{{ $ft->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="category_id" value="Categoria" />
        <x-ui.select name="category_id" id="category_id" class="mt-1" required>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $vehicle->category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
    </div>

    <!-- Campo de Secretaria -->
    <div>
        <x-input-label for="secretariat_id" value="Secretaria" />
        <x-ui.select name="secretariat_id" id="secretariat_id" class="mt-1" required>
            <option value="">Selecione...</option>
            @foreach($secretariats as $s)
                <option value="{{ $s->id }}" @selected(old('secretariat_id', $vehicle->secretariat_id ?? auth()->user()->secretariat_id) == $s->id)>{{ $s->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('secretariat_id')" class="mt-1" />
    </div>

    <!-- Campo de Prefixo com pesquisa inteligente -->
    <div x-data="prefixSearch('{{ $vehicle->prefix_id ?? '' }}', '{{ old('prefix_name', $vehicle->prefix->name ?? '') }}')">
        <x-input-label for="prefix_search" value="Prefixo *" />
        <div class="relative">
            <input
                type="text"
                id="prefix_search"
                x-model="searchQuery"
                @input="search()"
                @focus="showDropdown = true"
                @click.away="closeDropdown()"
                placeholder="Digite para pesquisar..."
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                autocomplete="off"
                required
            />
            <input type="hidden" name="prefix_id" x-model="selectedId" required>

            <!-- Dropdown de resultados -->
            <div x-show="showDropdown && (results.length > 0 || searchQuery.length > 0)"
                 x-cloak
                 x-transition
                 class="absolute z-50 w-full mt-1 bg-white dark:bg-navy-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto">

                <!-- Loading -->
                <div x-show="loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    Pesquisando...
                </div>

                <!-- Resultados -->
                <template x-for="result in results" :key="result.id">
                    <div @click="selectPrefix(result)"
                         class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-navy-700 text-sm text-gray-700 dark:text-gray-300">
                        <span x-text="result.name"></span>
                    </div>
                </template>

                <!-- Sem resultados -->
                <div x-show="!loading && results.length === 0 && searchQuery.length === 0"
                     class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    Digite para pesquisar prefixos
                </div>

                <!-- Opção para adicionar novo -->
                <div x-show="!loading && searchQuery.length > 0 && !exactMatch()"
                     @click="createNewPrefix()"
                     class="px-4 py-2 cursor-pointer bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 text-sm text-green-700 dark:text-green-400 border-t border-gray-200 dark:border-gray-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Adicionar "<span x-text="searchQuery"></span>"</span>
                </div>
            </div>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Digite para pesquisar ou criar um novo prefixo</p>
        <x-input-error :messages="$errors->get('prefix_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="status_id" value="Status" />
        <x-ui.select name="status_id" id="status_id" class="mt-1" required>
            @foreach($statuses as $s)
                <option value="{{ $s->id }}" @selected(old('status_id', $vehicle->status_id ?? '') == $s->id)>{{ $s->name ?? ('#'.$s->id) }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('status_id')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="chassis" value="Chassi" />
        <x-text-input id="chassis" name="chassis" type="text" class="mt-1 block w-full" :value="old('chassis', $vehicle->chassis ?? '')" />
        <x-input-error :messages="$errors->get('chassis')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="renavam" value="RENAVAM" />
        <x-text-input id="renavam" name="renavam" type="text" class="mt-1 block w-full" :value="old('renavam', $vehicle->renavam ?? '')" />
        <x-input-error :messages="$errors->get('renavam')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="registration" value="Registro" />
        <x-text-input id="registration" name="registration" type="text" class="mt-1 block w-full" :value="old('registration', $vehicle->registration ?? '')" />
        <x-input-error :messages="$errors->get('registration')" class="mt-1" />
    </div>
</div>

<script>
function prefixSearch(initialId, initialName) {
    return {
        searchQuery: initialName,
        selectedId: initialId,
        results: [],
        showDropdown: false,
        loading: false,
        searchTimeout: null,

        search() {
            clearTimeout(this.searchTimeout);

            if (this.searchQuery.length === 0) {
                this.results = [];
                this.selectedId = null;
                return;
            }

            this.searchTimeout = setTimeout(() => {
                this.loading = true;
                fetch(`{{ route('api.prefixes.search') }}?q=${encodeURIComponent(this.searchQuery)}`)
                    .then(response => response.json())
                    .then(data => {
                        this.results = data;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Erro ao buscar prefixos:', error);
                        this.loading = false;
                    });
            }, 300);
        },

        selectPrefix(prefix) {
            this.searchQuery = prefix.name;
            this.selectedId = prefix.id;
            this.showDropdown = false;
        },

        exactMatch() {
            return this.results.some(r => r.name.toLowerCase() === this.searchQuery.toLowerCase());
        },

        createNewPrefix() {
            if (!this.searchQuery || this.searchQuery.trim().length === 0) {
                return;
            }

            this.loading = true;

            fetch(`{{ route('api.prefixes.store-inline') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: this.searchQuery.trim()
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.selectPrefix(data.prefix);
                    this.showDropdown = false;
                    // Mostrar mensagem de sucesso
                    const successMsg = document.createElement('div');
                    successMsg.className = 'fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg';
                    successMsg.innerHTML = `
                        <strong class="font-bold">Sucesso!</strong>
                        <span class="block sm:inline">Prefixo "${data.prefix.name}" criado com sucesso.</span>
                    `;
                    document.body.appendChild(successMsg);
                    setTimeout(() => successMsg.remove(), 3000);
                } else {
                    alert(data.message || 'Erro ao criar prefixo. Pode já existir um com este nome.');
                }
                this.loading = false;
            })
            .catch(error => {
                console.error('Erro ao criar prefixo:', error);
                if (error.message) {
                    alert(error.message);
                } else if (error.errors && error.errors.name) {
                    alert('Erro: ' + error.errors.name[0]);
                } else {
                    alert('Erro ao criar prefixo. Verifique se você tem permissão ou se o prefixo já existe.');
                }
                this.loading = false;
            });
        },

        closeDropdown() {
            setTimeout(() => {
                this.showDropdown = false;
            }, 200);
        }
    }
}
</script>

<div class="flex items-center gap-3 pt-6">
    <x-primary-button icon="save" compact>Salvar</x-primary-button>
    <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
</div>
