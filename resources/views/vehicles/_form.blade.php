@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="Nome" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $vehicle->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <!-- Campo brand_id transformado em pesquisa com criação inline -->
    <div x-data="brandSearch('{{ $vehicle->brand_id ?? '' }}', '{{ old('brand_name', $vehicle->brand->name ?? '') }}')">
        <x-input-label for="brand_search" value="Marca *" />
        <div class="relative">
            <input
                type="text"
                id="brand_search"
                x-model="searchQuery"
                @input.debounce.300ms="search()"
                @focus="showDropdown = true"
                @click.away="closeDropdown()"
                placeholder="Digite para pesquisar ou criar..."
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                autocomplete="off"
                required
            />
            <input type="hidden" name="brand_id" x-model="selectedId" required>

            <!-- Dropdown de resultados -->
            <div x-show="showDropdown && (results.length > 0 || (searchQuery.length > 0 && !loading))"
                 x-cloak
                 x-transition
                 class="absolute z-40 w-full mt-1 bg-white dark:bg-navy-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto">

                <!-- Loading -->
                <div x-show="loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    Pesquisando...
                </div>

                <!-- Resultados -->
                <template x-for="result in results" :key="result.id">
                    <div @click="selectBrand(result)"
                         class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-navy-700 text-sm text-gray-700 dark:text-gray-300">
                        <span x-text="result.name"></span>
                    </div>
                </template>

                <!-- Sem resultados -->
                <div x-show="!loading && results.length === 0 && searchQuery.length === 0"
                     class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                    Digite para pesquisar marcas
                </div>

                <!-- Opção para adicionar novo -->
                <div x-show="!loading && searchQuery.length > 0 && !exactMatch()"
                     @click="createNewBrand()"
                     class="px-4 py-2 cursor-pointer bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 text-sm text-green-700 dark:text-green-400 border-t border-gray-200 dark:border-gray-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Adicionar "<span x-text="searchQuery"></span>"</span>
                </div>
            </div>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Digite para pesquisar ou criar uma nova marca</p>
        <x-input-error :messages="$errors->get('brand_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="heritage_id" value="Patrimônio" />
        <x-ui.select name="heritage_id" id="heritage_id" class="mt-1" required>
            <option value="">Selecione...</option>
            @foreach($heritages as $heritage)
                <option value="{{ $heritage->id }}" @selected(old('heritage_id', $vehicle->heritage_id ?? '') == $heritage->id)>{{ $heritage->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('heritage_id')" class="mt-1" />
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
            <option value="">Selecione...</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $vehicle->category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
    </div>

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
                @input.debounce.300ms="search()"
                @focus="showDropdown = true"
                @click.away="closeDropdown()"
                placeholder="Digite para pesquisar ou criar..."
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm"
                autocomplete="off"
                required
            />
            <input type="hidden" name="prefix_id" x-model="selectedId" required>

            <!-- Dropdown de resultados -->
            <div x-show="showDropdown && (results.length > 0 || (searchQuery.length > 0 && !loading))"
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

    <!-- Campo Status com "Disponível" como padrão -->
    @php
        $availableStatusId = $statuses->firstWhere('name', 'Disponível')?->id ?? '';
        $selectedStatusId = old('status_id', $vehicle->status_id ?? $availableStatusId);
    @endphp
    <div>
        <x-input-label for="status_id" value="Status" />
        <x-ui.select name="status_id" id="status_id" class="mt-1" required>
            @foreach($statuses as $s)
                <option value="{{ $s->id }}" @selected($selectedStatusId == $s->id)>
                    {{ $s->name ?? ('#'.$s->id) }}
                </option>
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
    function brandSearch(initialId, initialName) {
        return {
            searchQuery: initialName,
            selectedId: initialId,
            results: [],
            showDropdown: false,
            loading: false,
            searchTimeout: null,

            search() {
                clearTimeout(this.searchTimeout);
                this.selectedId = '';

                if (this.searchQuery.length === 0) {
                    this.results = [];
                    return;
                }

                this.searchTimeout = setTimeout(() => {
                    this.loading = true;
                    fetch(`{{ route('api.brands.search') }}?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(response => response.json())
                        .then(data => {
                            this.results = data;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Erro ao buscar marcas:', error);
                            this.loading = false;
                        });
                }, 300);
            },

            selectBrand(brand) {
                this.searchQuery = brand.name;
                this.selectedId = brand.id;
                this.showDropdown = false;
                this.results = [];
            },

            exactMatch() {
                return this.results.some(r => r.name.toLowerCase() === this.searchQuery.trim().toLowerCase());
            },

            createNewBrand() {
                const newBrandName = this.searchQuery.trim();
                if (!newBrandName) return;

                if (this.exactMatch()) {
                    const existingBrand = this.results.find(r => r.name.toLowerCase() === newBrandName.toLowerCase());
                    if (existingBrand) {
                        this.selectBrand(existingBrand);
                    }
                    this.showDropdown = false;
                    return;
                }

                this.loading = true;

                fetch(`{{ route('api.brands.store-inline') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: newBrandName })
                })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(({ status, body }) => {
                        if (status >= 200 && status < 300 && body.success) {
                            this.selectBrand(body.brand);
                            this.showDropdown = false;
                            this.showNotification('✅ Sucesso!', `Marca "${body.brand.name}" criada com sucesso.`, 'success');
                        } else {
                            const errorMsg = body.message || (body.errors && body.errors.name ? body.errors.name[0] : 'Erro desconhecido ao criar marca.');
                            this.showNotification('❌ Erro', errorMsg, 'error');
                            console.error('Erro no backend:', body);
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição:', error);
                        this.showNotification('❌ Erro', 'Falha na comunicação com o servidor.', 'error');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            showNotification(title, message, type = 'success') {
                const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-700 dark:text-green-400' : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-400';
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-[100] ${bgColor} border px-4 py-3 rounded-lg shadow-lg max-w-md animate-slide-in`;
                notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
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
            },

            closeDropdown() {
                setTimeout(() => { this.showDropdown = false; }, 150);
            },

            init() {
                if (this.selectedId && !this.searchQuery) {
                    // Lógica para buscar nome se só tiver ID (edição)
                }
            }
        }
    }

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
                this.selectedId = '';

                if (this.searchQuery.length === 0) {
                    this.results = [];
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
                this.results = [];
            },

            exactMatch() {
                return this.results.some(r => r.name.toLowerCase() === this.searchQuery.trim().toLowerCase());
            },

            createNewPrefix() {
                const newPrefixName = this.searchQuery.trim();
                if (!newPrefixName) return;

                if (this.exactMatch()) {
                    const existingPrefix = this.results.find(r => r.name.toLowerCase() === newPrefixName.toLowerCase());
                    if (existingPrefix) {
                        this.selectPrefix(existingPrefix);
                    }
                    this.showDropdown = false;
                    return;
                }

                this.loading = true;

                fetch(`{{ route('api.prefixes.store-inline') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: newPrefixName })
                })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(({ status, body }) => {
                        if (status >= 200 && status < 300 && body.success) {
                            this.selectPrefix(body.prefix);
                            this.showDropdown = false;
                            this.showNotification('✅ Sucesso!', `Prefixo "${body.prefix.name}" criado com sucesso.`, 'success');
                        } else {
                            const errorMsg = body.message || (body.errors && body.errors.name ? body.errors.name[0] : 'Erro desconhecido ao criar prefixo.');
                            this.showNotification('❌ Erro', errorMsg, 'error');
                            console.error('Erro no backend:', body);
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição:', error);
                        this.showNotification('❌ Erro', 'Falha na comunicação com o servidor.', 'error');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            showNotification(title, message, type = 'success') {
                const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-700 dark:text-green-400' : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-400';
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-[100] ${bgColor} border px-4 py-3 rounded-lg shadow-lg max-w-md animate-slide-in`;
                notification.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
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
            },

            closeDropdown() {
                setTimeout(() => { this.showDropdown = false; }, 150);
            },

            init() {
                if (this.selectedId && !this.searchQuery) {
                    // Lógica para buscar nome se só tiver ID (edição)
                }
            }
        }
    }
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease forwards;
    }
</style>

<div class="flex items-center gap-3 pt-6">
    <x-primary-button icon="save" compact>Salvar</x-primary-button>
    <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
</div>
