{{-- Links de Navegação Sidebar --}}
@php($vehicleGroupActive = request()->routeIs('vehicles.*') || request()->routeIs('vehicle-categories.*') || request()->routeIs('prefixes.*') || request()->routeIs('vehicle-transfers.*') || request()->routeIs('vehicles.usage-panel') || request()->routeIs('vehicle-price-origins.*'))
@php($logbookGroupActive = request()->routeIs('logbook.*') || request()->routeIs('logbook-permissions.*') || request()->routeIs('logbook-rules.*'))
@php($checklistGroupActive = request()->routeIs('checklists.*') || request()->routeIs('defect-reports.*'))
@php($maintenanceGroupActive = request()->routeIs('oil-changes.*') || request()->routeIs('tires.*'))
@php($fuelGroupActive = request()->routeIs('fuel-quotations.*', 'gas-stations.*', 'scheduled_gas_stations.*', 'gas_stations_current.*', 'scheduled_prices.*', 'fuel_prices.*'))
@php($reportsGroupActive = request()->routeIs('backup-reports.*') || request()->routeIs('pdf-templates.*'))
@php($usersGroupActive = request()->routeIs('users.*') || request()->routeIs('default-passwords.*'))
@php($auditGroupActive = request()->routeIs('audit-logs.*'))
@php($finesGroupActive = request()->routeIs('fines.*'))
{{-- CSS inline para controle visual --}}
<style>
    /* Apenas aplicar transformações visuais que não conflitam com Alpine */
    @if($vehicleGroupActive)
        #nav-vehicles-chevron { transform: rotate(180deg); }
    @endif
    @if($logbookGroupActive)
        #nav-logbook-chevron { transform: rotate(180deg); }
    @endif
    @if($checklistGroupActive)
        #nav-checklist-chevron { transform: rotate(180deg); }
    @endif
    @if($maintenanceGroupActive)
        #nav-maintenance-chevron { transform: rotate(180deg); }
    @endif
    @if($fuelGroupActive)
        #nav-fuel-chevron { transform: rotate(180deg); }
    @endif
    @if($reportsGroupActive)
        #nav-reports-chevron { transform: rotate(180deg); }
    @endif
    @if($usersGroupActive)
        #nav-users-chevron { transform: rotate(180deg); }
    @endif
    @if($auditGroupActive)
        #nav-audit-chevron { transform: rotate(180deg); }
    @endif
    /* Esconder elementos x-cloak ANTES do Alpine carregar */
    [x-cloak] { display: none !important; }
    /* Transições suaves para submenus */
    .submenu-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    /* REMOVIDO: CSS que escondia todos os submenus - isso estava impedindo a abertura */
</style>
<ul class="mt-4 space-y-1"
    x-data="{
        vehiclesOpen: {{ $vehicleGroupActive ? 'true' : 'false' }},
        logbookOpen: {{ $logbookGroupActive ? 'true' : 'false' }},
        checklistOpen: {{ $checklistGroupActive ? 'true' : 'false' }},
        maintenanceOpen: {{ $maintenanceGroupActive ? 'true' : 'false' }},
        fuelOpen: {{ $fuelGroupActive ? 'true' : 'false' }},
        reportsOpen: {{ $reportsGroupActive ? 'true' : 'false' }},
        usersOpen: {{ $usersGroupActive ? 'true' : 'false' }},
        auditOpen: {{ $auditGroupActive ? 'true' : 'false' }},
        finesOpen: {{ $finesGroupActive ? 'true' : 'false' }},
        // Adicionar estas variáveis para controlar submenus quando colapsado
        logbookSubmenuOpen: false,
        checklistSubmenuOpen: false,
        vehiclesSubmenuOpen: false,
        maintenanceSubmenuOpen: false,
        fuelSubmenuOpen: false,
        reportsSubmenuOpen: false,
        usersSubmenuOpen: false,
        auditSubmenuOpen: false,
        finesSubmenuOpen: false,
        // Função para fechar outros menus quando um abre
        closeOtherMenus(except) {
            const menus = ['vehicles', 'logbook', 'checklist', 'maintenance', 'fuel', 'reports', 'users', 'audit', 'fines'];
            menus.forEach(menu => {
                if (menu !== except) {
                    this[menu + 'Open'] = false;
                }
            });
        }
    }"
    x-init="() => {
    // Inicialização do estado dos menus
    @if(!$vehicleGroupActive)
        if (localStorage.getItem('nav-vehicles-open') !== null) {
            this.vehiclesOpen = localStorage.getItem('nav-vehicles-open') === 'true';
        }
    @endif
    @if(!$logbookGroupActive)
        if (localStorage.getItem('nav-logbook-open') !== null) {
            this.logbookOpen = localStorage.getItem('nav-logbook-open') === 'true';
        }
    @endif
    @if(!$checklistGroupActive)
        if (localStorage.getItem('nav-checklist-open') !== null) {
            this.checklistOpen = localStorage.getItem('nav-checklist-open') === 'true';
        }
    @endif
    @if(!$maintenanceGroupActive)
        if (localStorage.getItem('nav-maintenance-open') !== null) {
            this.maintenanceOpen = localStorage.getItem('nav-maintenance-open') === 'true';
        }
    @endif
    @if(!$fuelGroupActive)
        if (localStorage.getItem('nav-fuel-open') !== null) {
            this.fuelOpen = localStorage.getItem('nav-fuel-open') === 'true';
        }
    @endif
    @if(!$reportsGroupActive)
        if (localStorage.getItem('nav-reports-open') !== null) {
            this.reportsOpen = localStorage.getItem('nav-reports-open') === 'true';
        }
    @endif
    @if(!$usersGroupActive)
        if (localStorage.getItem('nav-users-open') !== null) {
            this.usersOpen = localStorage.getItem('nav-users-open') === 'true';
        }
    @endif
    @if(!$auditGroupActive)
        if (localStorage.getItem('nav-audit-open') !== null) {
            this.auditOpen = localStorage.getItem('nav-audit-open') === 'true';
        }
    @endif
    // Watchers para persistência e controle de menus
    $watch('vehiclesOpen', value => {
        if (!{{ $vehicleGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-vehicles-open', value);
        if (value) this.closeOtherMenus('vehicles');
    });
    $watch('logbookOpen', value => {
        if (!{{ $logbookGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-logbook-open', value);
        if (value) this.closeOtherMenus('logbook');
    });
    $watch('checklistOpen', value => {
        if (!{{ $checklistGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-checklist-open', value);
        if (value) this.closeOtherMenus('checklist');
    });
    $watch('maintenanceOpen', value => {
        if (!{{ $maintenanceGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-maintenance-open', value);
        if (value) this.closeOtherMenus('maintenance');
    });
    $watch('fuelOpen', value => {
        if (!{{ $fuelGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-fuel-open', value);
        if (value) this.closeOtherMenus('fuel');
    });
    $watch('reportsOpen', value => {
        if (!{{ $reportsGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-reports-open', value);
        if (value) this.closeOtherMenus('reports');
    });
    $watch('usersOpen', value => {
        if (!{{ $usersGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-users-open', value);
        if (value) this.closeOtherMenus('users');
    });
    $watch('auditOpen', value => {
        if (!{{ $auditGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-audit-open', value);
        if (value) this.closeOtherMenus('audit');
    });
    $watch('finesOpen', value => {
        if (!{{ $finesGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-fines-open', value);
        if (value) this.closeOtherMenus('fines');
    });
}"
>
    <!-- Dashboard -->
    <li class="relative group">
        @if(request()->routeIs('dashboard'))
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200
                  {{ request()->routeIs('dashboard') ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="dashboard" class="w-5 h-5 shrink-0" />
            <span class="truncate" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Dashboard</span>
            <!-- Tooltip quando colapsado -->
            <span x-cloak x-show="isSidebarCollapsed && !isMobileSidebarOpen" class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-primary-600 text-white text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow z-50">Dashboard</span>
        </a>
    </li>
    <!-- Diário de Bordo -->
    <li class="relative group">
        @if($logbookGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ logbookSubmenuOpen = !logbookSubmenuOpen; } else { logbookOpen = !logbookOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ logbookSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $logbookGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="clipboard" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Diário de Bordo</span>
            <x-icon name="chevron-down" id="nav-logbook-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="logbookOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>
        <ul id="nav-logbook-submenu" x-show="logbookOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            <li>
                <a href="{{ route('logbook.start-flow') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook.start-flow') || request()->routeIs('logbook.vehicle-select') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> <span>Nova Corrida</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logbook.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="list" class="w-3.5 h-3.5" /> <span>Minhas Corridas</span>
                </a>
            </li>
            @if(auth()->user()->isGeneralManager())
                <li>
                    <a href="{{ route('logbook-permissions.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook-permissions.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="shield" class="w-3.5 h-3.5" /> <span>Privilégios</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->isGeneralManager())
                <li>
                    <a href="{{ route('logbook-rules.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
        {{ request()->routeIs('logbook-rules.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="cog" class="w-3.5 h-3.5" /> <span>Regras de KM</span>
                    </a>
                </li>
            @endif
        </ul>
        <!-- Submenu popup quando colapsada -->
        <div x-cloak
             x-show="logbookSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                Diário de Bordo
            </div>
            <a href="{{ route('logbook.index') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('logbook.index') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                <x-icon name="list" class="w-4 h-4" />
                <span>Minhas Corridas</span>
            </a>
            <a href="{{ route('logbook.start-flow') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('logbook-start-flow') || request()->routeIs('logbook.vehicle-select') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Nova Corrida</span>
            </a>
            @if(auth()->user()->isGeneralManager())
                <a href="{{ route('logbook-permissions.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('logbook-permissions.*') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="shield" class="w-4 h-4" />
                    <span>Privilégios</span>
                </a>
            @endif

        </div>
    </li>
    <!-- Checklists (Notificações) -->
    <li class="relative group">
        @if($checklistGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ checklistSubmenuOpen = !checklistSubmenuOpen; } else { checklistOpen = !checklistOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ checklistSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $checklistGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="clipboard-check" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Checklists</span>
            <x-icon name="chevron-down" id="nav-checklist-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="checklistOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>
        <ul id="nav-checklist-submenu" x-show="checklistOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            <li>
                <a href="{{ route('checklists.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('checklists.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="list" class="w-3.5 h-3.5" /> <span>Todos</span>
                </a>
            </li>
            {{-- Link para Comunicação de Defeitos --}}
            <li>
                <a href="{{ route('defect-reports.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('defect-reports.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="alert" class="w-3.5 h-3.5" /> <span>Comunicar Defeito</span>
                </a>
            </li>
            @if(auth()->user()->isManager())
                <li>
                    <a href="{{ route('checklists.pending') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('checklists.pending') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="bell" class="w-3.5 h-3.5" /> <span>Pendentes</span>
                    </a>
                </li>
            @endif
        </ul>
        <!-- Submenu popup quando colapsada -->
        <div x-cloak
             x-show="checklistSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                Checklists
            </div>
            <a href="{{ route('checklists.index') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('checklists.index') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                <x-icon name="list" class="w-4 h-4" />
                <span>Todos</span>
            </a>
            @if(auth()->user()->isManager())
                <a href="{{ route('checklists.pending') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('checklists.pending') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="bell" class="w-4 h-4" />
                    <span>Pendentes</span>
                </a>
            @endif
        </div>
    </li>
    <!-- Grupo Veículos -->
    <li class="relative group">
        @if($vehicleGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ vehiclesSubmenuOpen = !vehiclesSubmenuOpen; } else { vehiclesOpen = !vehiclesOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ vehiclesSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $vehicleGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="car" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Veículos</span>
            <x-icon name="chevron-down" id="nav-vehicles-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="vehiclesOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>
        <ul id="nav-vehicles-submenu" x-show="vehiclesOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            @if(!auth()->user()->hasRole('driver'))
                <li>
                    <a href="{{ route('vehicles.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicles.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="list" class="w-3.5 h-3.5" /> <span>Gerenciar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('vehicles.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicles.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> <span>Cadastrar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('vehicle-categories.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicle-categories.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="category" class="w-3.5 h-3.5" /> <span>Categorias</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('prefixes.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('prefixes.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="prefix" class="w-3.5 h-3.5" /> <span>Prefixos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('vehicle-price-origins.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
        {{ request()->routeIs('vehicle-price-origins.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="currency-dollar" class="w-3.5 h-3.5" /> <span>Patrimônios</span>
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('vehicle-transfers.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicle-transfers.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="swap" class="w-3.5 h-3.5" /> <span>Transferências</span>
                </a>
            </li>
                @if(auth()->user()->isManager())
                    <li>
                        <a href="{{ route('vehicles.usage-panel') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
            {{ request()->routeIs('vehicles.usage-panel') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="chart-bar" class="w-3.5 h-3.5" /> <span>Veículos em Uso</span>
                        </a>
                    </li>
                @endif
        </ul>
        <!-- Submenu popup quando colapsada -->
        <div x-cloak
             x-show="vehiclesSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                Veículos
            </div>
            @if(!auth()->user()->hasRole('driver'))
                <a href="{{ route('vehicles.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('vehicles.index') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="list" class="w-4 h-4" />
                    <span>Gerenciar</span>
                </a>
                <a href="{{ route('vehicles.create') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('vehicles.create') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Cadastrar</span>
                </a>
                <a href="{{ route('vehicle-categories.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('vehicle-categories.*') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="category" class="w-4 h-4" />
                    <span>Categorias</span>
                </a>
                <a href="{{ route('prefixes.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('prefixes.*') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="prefix" class="w-4 h-4" />
                    <span>Prefixos</span>
                </a>
            @endif
            <a href="{{ route('vehicle-transfers.index') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('vehicle-transfers.*') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                <x-icon name="swap" class="w-4 h-4" />
                <span>Transferências</span>
            </a>
        </div>
    </li>
    <!-- Manutenção -->
    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($maintenanceGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ maintenanceSubmenuOpen = !maintenanceSubmenuOpen; } else { maintenanceOpen = !maintenanceOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ maintenanceSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $maintenanceGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Manutenção</span>
                <x-icon name="chevron-down" id="nav-maintenance-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="maintenanceOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
            </button>
            <ul id="nav-maintenance-submenu" x-show="maintenanceOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <!-- Troca de Óleo -->
                <li>
                    <a href="{{ route('oil-changes.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('oil-changes.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span>Troca de Óleo</span>
                    </a>
                </li>
                @if(auth()->user()->isGeneralManager())
                    <li>
                        <a href="{{ route('oil-changes.settings') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('oil-changes.settings') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="settings" class="w-3.5 h-3.5" />
                            <span>Configurações Óleo</span>
                        </a>
                    </li>
                @endif
                <!-- Troca de Pneus -->
                <li>
                    <a href="{{ route('tires.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h.01M15 10h.01M9 14h6"/>
                        </svg>
                        <span>Dashboard Pneus</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tires.vehicles') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.vehicles') || request()->routeIs('tires.vehicles.show') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="car" class="w-3.5 h-3.5" />
                        <span>Veículos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tires.stock') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.stock') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Estoque</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tires.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus" class="w-3.5 h-3.5" />
                        <span>Cadastrar Pneu</span>
                    </a>
                </li>
            </ul>
            <!-- Submenu popup quando colapsada -->
            <div x-cloak
                 x-show="maintenanceSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                    Manutenção
                </div>
                <a href="{{ route('oil-changes.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('oil-changes.index') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span>Troca de Óleo</span>
                </a>
                <!-- Troca de Pneus -->
                <a href="{{ route('tires.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('tires.index') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h.01M15 10h.01M9 14h6"/>
                    </svg>
                    <span>Dashboard Pneus</span>
                </a>
            </div>
        </li>
    @endif
    <!-- Combustível -->
    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($fuelGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ fuelSubmenuOpen = !fuelSubmenuOpen; } else { fuelOpen = !fuelOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ fuelSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
        {{ $fuelGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Combustível</span>
                <x-icon name="chevron-down" id="nav-fuel-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="fuelOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
            </button>
            <ul id="nav-fuel-submenu" x-show="fuelOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                {{-- Links existentes --}}
                <li>
                    <a href="{{ route('gas-stations.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('gas-stations.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Postos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('gas_stations_current.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('gas_stations_current.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="clipboard-check" class="w-3.5 h-3.5" />
                        <span>Postos Ativos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('scheduled_gas_stations.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('scheduled_gas_stations.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="calendar" class="w-3.5 h-3.5" />
                        <span>Agendamentos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('fuel_prices.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel_prices.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="currency-dollar" class="w-3.5 h-3.5" />
                        <span>Preços Atuais</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('scheduled_prices.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('scheduled_prices.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="calendar" class="w-3.5 h-3.5" />
                        <span>Agendamento de Preços</span>
                    </a>
                </li>

                {{-- Links existentes --}}
                <li>
                    <a href="{{ route('fuel-quotations.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel-quotations.index') || request()->routeIs('fuel-quotations.show') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="list" class="w-3.5 h-3.5" /> <span>Cotações</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('fuel-quotations.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel-quotations.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> <span>Nova Cotação</span>
                    </a>
                </li>
                @if(auth()->user()->isGeneralManager())
                    <li>
                        <a href="{{ route('fuel-quotations.settings') }}"
                           class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel-quotations.settings') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="settings" class="w-3.5 h-3.5" />
                            <span>Configurações</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif
    {{-- Insira este bloco no local desejado na sua lista <ul> --}}
    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($finesGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ finesSubmenuOpen = !finesSubmenuOpen; } else { finesOpen = !finesOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ finesSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
        {{ $finesGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="speed-camera" class="w-5 h-5 shrink-0" />
                <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Multas</span>
                <x-icon name="chevron-down" id="nav-fines-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="finesOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
            </button>
            <ul id="nav-fines-submenu" x-show="finesOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <li>
                    <a href="{{ route('fines.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fines.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="list" class="w-3.5 h-3.5" /> <span>Gerenciar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('fines.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fines.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> <span>Cadastrar</span>
                    </a>
                </li>
            </ul>
            <div x-cloak
                 x-show="finesSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
                 x-transition
                 class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                    Multas
                </div>
                <a href="{{ route('fines.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('fines.index') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="list" class="w-4 h-4" />
                    <span>Gerenciar</span>
                </a>
                <a href="{{ route('fines.create') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('fines.create') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Cadastrar</span>
                </a>
            </div>
        </li>
    @endif
    <!-- Relatórios / Backups -->
    <li class="relative group">
        @if($reportsGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ reportsSubmenuOpen = !reportsSubmenuOpen; } else { reportsOpen = !reportsOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ reportsSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $reportsGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="document" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Relatórios</span>
            <x-icon name="chevron-down" id="nav-reports-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="reportsOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>
        <ul id="nav-reports-submenu" x-show="reportsOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            <li>
                <a href="{{ route('backup-reports.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('backup-reports.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="list" class="w-3.5 h-3.5" /> <span>Backups</span>
                </a>
            </li>
            @if(auth()->user()->isGeneralManager())
                <li>
                    <a href="{{ route('pdf-templates.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('pdf-templates.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="template" class="w-3.5 h-3.5" /> <span>Modelos</span>
                    </a>
                </li>
            @endif
        </ul>
        <!-- Submenu popup quando colapsada -->
        <div x-cloak
             x-show="reportsSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                Relatórios
            </div>
            <a href="{{ route('backup-reports.index') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('backup-reports.*') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                <x-icon name="list" class="w-4 h-4" />
                <span>Backups</span>
            </a>
            @if(auth()->user()->isGeneralManager())
                <a href="{{ route('pdf-templates.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('pdf-templates.*') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="template" class="w-4 h-4" />
                    <span>Modelos</span>
                </a>
            @endif
        </div>
    </li>
    <!-- Usuários -->
    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($usersGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ usersSubmenuOpen = !usersSubmenuOpen; } else { usersOpen = !usersOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ usersSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $usersGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="users" class="w-5 h-5 shrink-0" />
                <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Usuários</span>
                <x-icon name="chevron-down" id="nav-users-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="usersOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
            </button>
            <ul id="nav-users-submenu" x-show="usersOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <li>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('users.*') && !request()->routeIs('users.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="list" class="w-3.5 h-3.5" /> <span>Gerenciar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('users.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> <span>Cadastrar</span>
                    </a>
                </li>
                @if(auth()->user()->isGeneralManager())
                    <li>
                        <a href="{{ route('default-passwords.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('default-passwords.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="key" class="w-3.5 h-3.5" /> <span>Senhas Padrão</span>
                        </a>
                    </li>
                @endif
            </ul>
            <!-- Submenu popup quando colapsada -->
            <div x-cloak
                 x-show="usersSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                    Usuários
                </div>
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('users.*') && !request()->routeIs('users.create') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="list" class="w-4 h-4" />
                    <span>Gerenciar</span>
                </a>
                <a href="{{ route('users.create') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('users.create') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Cadastrar</span>
                </a>
                @if(auth()->user()->isGeneralManager())
                    <a href="{{ route('default-passwords.index') }}"
                       class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('default-passwords.*') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                        <x-icon name="key" class="w-4 h-4" />
                        <span>Senhas Padrão</span>
                    </a>
                @endif
            </div>
        </li>
    @endif
    <!-- Auditoria -->
    @if(auth()->user()->isGeneralManager())
        <li class="relative group">
            @if($auditGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ auditSubmenuOpen = !auditSubmenuOpen; } else { auditOpen = !auditOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ auditSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $auditGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="clipboard" class="w-5 h-5 shrink-0" />
                <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Auditoria</span>
                <x-icon name="chevron-down" id="nav-audit-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="auditOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
            </button>
            <ul id="nav-audit-submenu" x-show="auditOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <li>
                    <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('audit-logs.index') && !request()->input('type') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="list" class="w-3.5 h-3.5" /> <span>Todos os Logs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('audit-logs.index', ['type' => 'App\Models\User']) }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->input('type') == 'App\Models\User' ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="users" class="w-3.5 h-3.5" /> <span>Usuários</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('audit-logs.index', ['type' => 'App\Models\Vehicle']) }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->input('type') == 'App\Models\Vehicle' ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="car" class="w-3.5 h-3.5" /> <span>Veículos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('audit-logs.index', ['type' => 'App\Models\Run']) }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->input('type') == 'App\Models\Run' ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="route" class="w-3.5 h-3.5" /> <span>Rodagens</span>
                    </a>
                </li>
            </ul>
            <!-- Submenu popup quando colapsada -->
            <div x-cloak
                 x-show="auditSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                    Auditoria
                </div>
                <a href="{{ route('audit-logs.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('audit-logs.index') && !request()->input('type') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="list" class="w-4 h-4" />
                    <span>Todos os Logs</span>
                </a>
                <a href="{{ route('audit-logs.index', ['type' => 'App\Models\User']) }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->input('type') == 'App\Models\User' ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="users" class="w-4 h-4" />
                    <span>Usuários</span>
                </a>
                <a href="{{ route('audit-logs.index', ['type' => 'App\Models\Vehicle']) }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->input('type') == 'App\Models\Vehicle' ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="car" class="w-4 h-4" />
                    <span>Veículos</span>
                </a>
                <a href="{{ route('audit-logs.index', ['type' => 'App\Models\Run']) }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->input('type') == 'App\Models\Run' ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="route" class="w-4 h-4" />
                    <span>Rodagens</span>
                </a>
            </div>
        </li>


    @endif

    <li class="relative group">
        @if(request()->routeIs('chat.*'))
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <a href="{{ route('chat.index') }}"
           class="flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200
                  {{ request()->routeIs('chat.*') ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span class="truncate" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Chat</span>
            <span x-cloak x-show="isSidebarCollapsed && !isMobileSidebarOpen" class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-primary-600 text-white text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow z-50">Chat</span>
        </a>
    </li>
</ul>
