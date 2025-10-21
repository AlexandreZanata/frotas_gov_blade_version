{{-- Links de Navegação Sidebar --}}
@php($vehicleGroupActive = request()->routeIs('vehicles.*') || request()->routeIs('vehicle-categories.*') || request()->routeIs('prefixes.*') || request()->routeIs('vehicle-transfers.*') || request()->routeIs('vehicles.usage-panel') || request()->routeIs('vehicle-price-origins.*'))
@php($logbookGroupActive = request()->routeIs('logbook.*') || request()->routeIs('logbook-permissions.*') || request()->routeIs('logbook-rules.*'))
@php($checklistGroupActive = request()->routeIs('checklists.*') || request()->routeIs('defect-reports.*'))
@php($maintenanceGroupActive = request()->routeIs('oil-changes.*') || request()->routeIs('tires.*'))
@php($fuelGroupActive = request()->routeIs('fuel-quotations.*', 'gas-stations.*', 'scheduled_gas_stations.*', 'gas_stations_current.*', 'scheduled_prices.*', 'fuel_prices.*', 'fueling_expenses.*'))@php($reportsGroupActive = request()->routeIs('backup-reports.*') || request()->routeIs('pdf-templates.*'))
@php($usersGroupActive = request()->routeIs('users.*') || request()->routeIs('default-passwords.*'))
@php($auditGroupActive = request()->routeIs('audit-logs.*'))
@php($finesGroupActive = request()->routeIs('fines.*'))
@php($garbageGroupActive = request()->routeIs('admin.garbage-users.*') || request()->routeIs('garbage-logbook.*') || request()->routeIs('admin.garbage-vehicles.*') || request()->routeIs('admin.garbage-neighborhoods.*') || request()->routeIs('admin.garbage-reports.*'))
{{-- CSS inline para controle visual --}}
<style>
    /* Apenas aplicar transformações visuais que não conflitam com Alpine */
    @if($vehicleGroupActive)
        #nav-vehicles-chevron {
        transform: rotate(180deg);
    }

    @endif
    @if($logbookGroupActive)
        #nav-logbook-chevron {
        transform: rotate(180deg);
    }

    @endif
    @if($checklistGroupActive)
        #nav-checklist-chevron {
        transform: rotate(180deg);
    }

    @endif
    @if($maintenanceGroupActive)
        #nav-maintenance-chevron {
        transform: rotate(180deg);
    }

    @endif
    @if($fuelGroupActive)
        #nav-fuel-chevron {
        transform: rotate(180deg);
    }

    @endif
    @if($reportsGroupActive)
        #nav-reports-chevron {
        transform: rotate(180deg);
    }

    @endif
    @if($usersGroupActive)
        #nav-users-chevron {
        transform: rotate(180deg);
    }

    @endif
    @if($auditGroupActive)
        #nav-audit-chevron {
        transform: rotate(180deg);
    }
    @if($garbageGroupActive)
    #nav-garbage-chevron {
        transform: rotate(180deg);
    }
    @endif

        @endif
/* Esconder elementos x-cloak ANTES do Alpine carregar */
    [x-cloak] {
        display: none !important;
    }

    /* Transições suaves para submenus */
    .submenu-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

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
        garbageOpen: {{ $garbageGroupActive ? 'true' : 'false' }},

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
        garbageSubmenuOpen: false,
        // Função para fechar outros menus quando um abre
        closeOtherMenus(except) {
            const menus = ['vehicles', 'logbook', 'checklist', 'maintenance', 'fuel', 'reports', 'users', 'audit', 'fines', 'garbage'];
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
        @if(!$garbageGroupActive)
        if (localStorage.getItem('nav-garbage-open') !== null) {
            this.garbageOpen = localStorage.getItem('nav-garbage-open') === 'true';
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
    $watch('garbageOpen', value => {
        if (!{{ $garbageGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-garbage-open', value);
        if (value) this.closeOtherMenus('garbage');
    });
}"
>
    <!-- Dashboard -->
    <li class="relative group">
        @if(request()->routeIs('dashboard'))
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                  aria-hidden="true"></span>
        @endif
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200
                  {{ request()->routeIs('dashboard') ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="house" class="w-5 h-5 shrink-0"/>
            <span class="truncate" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Dashboard</span>
            <!-- Tooltip quando colapsado -->
            <span x-cloak x-show="isSidebarCollapsed && !isMobileSidebarOpen"
                  class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-primary-600 text-white text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow z-50">Dashboard</span>
        </a>
    </li>
    <!-- Diário de Bordo -->
    <li class="relative group">
        @if($logbookGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                  aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ logbookSubmenuOpen = !logbookSubmenuOpen; } else { logbookOpen = !logbookOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ logbookSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $logbookGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="journal-bookmark" class="w-5 h-5 shrink-0"/>
            <span class="truncate flex-1 text-left"
                  x-show="!isSidebarCollapsed || isMobileSidebarOpen">Diário de Bordo</span>
            <x-icon name="chevron-down" id="nav-logbook-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                    x-bind:class="logbookOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
        </button>
        <ul id="nav-logbook-submenu" x-show="logbookOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
            class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            <li>
                <a href="{{ route('logbook.start-flow') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook.start-flow') || request()->routeIs('logbook.vehicle-select') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="journal-arrow-up" class="w-3.5 h-3.5"/>
                    <span>Nova Corrida</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logbook.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="book" class="w-3.5 h-3.5"/>
                    <span>Minhas Corridas</span>
                </a>
            </li>
            @if(auth()->user()->isGeneralManager())
                <li>
                    <a href="{{ route('logbook-permissions.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook-permissions.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="shield" class="w-3.5 h-3.5"/>
                        <span>Privilégios</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->isGeneralManager())
                <li>
                    <a href="{{ route('logbook-rules.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
        {{ request()->routeIs('logbook-rules.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="gear" class="w-3.5 h-3.5"/>
                        <span>Regras de KM</span>
                    </a>
                </li>
            @endif
        </ul>

    </li>

    {{-- Administração de Resíduos --}}
    <li class="relative group">
        @if($garbageGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                  aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ garbageSubmenuOpen = !garbageSubmenuOpen; } else { garbageOpen = !garbageOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ garbageSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
        {{ $garbageGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="trash" class="w-5 h-5 shrink-0"/>
            <span class="truncate flex-1 text-left"
                  x-show="!isSidebarCollapsed || isMobileSidebarOpen">Administração de Resíduos</span>
            <x-icon name="chevron-down" id="nav-garbage-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                    x-bind:class="garbageOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
        </button>
        <ul id="nav-garbage-submenu" x-show="garbageOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
            class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            {{-- Diário de Bordo de Coleta --}}
            <li>
                <a href="{{ route('garbage-logbook.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('garbage-logbook.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="journal-arrow-up" class="w-3.5 h-3.5"/>
                    <span>Diário de Coleta</span>
                </a>
            </li>

            {{-- Administração (apenas para gestores) --}}
            @if(auth()->user()->isManager() || auth()->user()->isGeneralManager())
                <li>
                    <a href="{{ route('admin.garbage-users.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('admin.garbage-users.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="users" class="w-3.5 h-3.5"/>
                        <span>Gerenciar Usuários</span>
                    </a>
                </li>

                {{-- Veículos de Lixo --}}
                <li>
                    <a href="{{ route('admin.garbage-vehicles.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('admin.garbage-vehicles.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="truck" class="w-3.5 h-3.5"/>
                        <span>Veículos de Lixo</span>
                    </a>
                </li>

                {{-- Bairros --}}
                <li>
                    <a href="{{ route('admin.garbage-neighborhoods.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('admin.garbage-neighborhoods.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="map" class="w-3.5 h-3.5"/>
                        <span>Bairros</span>
                    </a>
                </li>

                {{-- Relatórios de Coleta --}}
                <li>
                    <a href="{{ route('admin.garbage-reports.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('admin.garbage-reports.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="bar-chart-line" class="w-3.5 h-3.5"/>
                        <span>Relatórios</span>
                    </a>
                </li>
            @endif
        </ul>

        {{-- Submenu para sidebar colapsada --}}
        <div x-show="isSidebarCollapsed && !isMobileSidebarOpen && garbageSubmenuOpen"
             @click.away="garbageSubmenuOpen = false"
             class="absolute left-full top-0 ml-1 z-50 w-48 rounded-md shadow-lg bg-white dark:bg-navy-800 ring-1 ring-black ring-opacity-5">
            <div class="py-1">
                {{-- Diário de Bordo de Coleta --}}
                <a href="{{ route('garbage-logbook.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-gray-100 dark:hover:bg-navy-700">
                    <x-icon name="clipboard" class="w-4 h-4"/>
                    <span>Diário de Coleta</span>
                </a>

                @if(auth()->user()->isManager() || auth()->user()->isGeneralManager())
                    {{-- Administração --}}
                    <a href="{{ route('admin.garbage-users.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-gray-100 dark:hover:bg-navy-700">
                        <x-icon name="people" class="w-4 h-4"/>
                        <span>Gerenciar Usuários</span>
                    </a>

                    {{-- Veículos --}}
                    <a href="{{ route('admin.garbage-vehicles.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-gray-100 dark:hover:bg-navy-700">
                        <x-icon name="truck" class="w-4 h-4"/>
                        <span>Veículos de Lixo</span>
                    </a>

                    {{-- Bairros --}}
                    <a href="{{ route('admin.garbage-neighborhoods.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-gray-100 dark:hover:bg-navy-700">
                        <x-icon name="geo-alt" class="w-4 h-4"/>
                        <span>Bairros</span>
                    </a>

                    {{-- Relatórios --}}
                    <a href="{{ route('admin.garbage-reports.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-gray-100 dark:hover:bg-navy-700">
                        <x-icon name="chart-bar" class="w-4 h-4"/>
                        <span>Relatórios</span>
                    </a>
                @endif
            </div>
        </div>
    </li>
    <!-- Checklists (Notificações) -->
    <li class="relative group">
        @if($checklistGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                  aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ checklistSubmenuOpen = !checklistSubmenuOpen; } else { checklistOpen = !checklistOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ checklistSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $checklistGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="checklist" class="w-5 h-5 shrink-0"/>
            <span class="truncate flex-1 text-left"
                  x-show="!isSidebarCollapsed || isMobileSidebarOpen">Checklists</span>
            <x-icon name="chevron-down" id="nav-checklist-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                    x-bind:class="checklistOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
        </button>
        <ul id="nav-checklist-submenu" x-show="checklistOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
            class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            <li>
                <a href="{{ route('checklists.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('checklists.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="checklist" class="w-3.5 h-3.5"/>
                    <span>Todos</span>
                </a>
            </li>
            {{-- Link para Comunicação de Defeitos --}}
            <li>
                <a href="{{ route('defect-reports.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('defect-reports.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="alert" class="w-3.5 h-3.5"/>
                    <span>Comunicar Defeito</span>
                </a>
            </li>
            @if(auth()->user()->isManager())
                <li>
                    <a href="{{ route('checklists.pending') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('checklists.pending') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="bell" class="w-3.5 h-3.5"/>
                        <span>Pendentes</span>
                    </a>
                </li>
            @endif
        </ul>

    </li>
    <!-- Grupo Veículos -->
    <li class="relative group">
        @if($vehicleGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                  aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ vehiclesSubmenuOpen = !vehiclesSubmenuOpen; } else { vehiclesOpen = !vehiclesOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ vehiclesSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $vehicleGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="truck" class="w-5 h-5 shrink-0"/>
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Veículos</span>
            <x-icon name="chevron-down" id="nav-vehicles-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                    x-bind:class="vehiclesOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
        </button>
        <ul id="nav-vehicles-submenu" x-show="vehiclesOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
            class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            @if(!auth()->user()->hasRole('driver'))
                <li>
                    <a href="{{ route('vehicles.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicles.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="diagram-3" class="w-3.5 h-3.5"/>
                        <span>Gerenciar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('vehicles.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicles.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus-circle" class="w-3.5 h-3.5"/>
                        <span>Cadastrar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('vehicle-categories.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicle-categories.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="ui-checks-grid" class="w-3.5 h-3.5"/>
                        <span>Categorias</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('prefixes.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('prefixes.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="pass" class="w-3.5 h-3.5"/>
                        <span>Prefixos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('vehicle-price-origins.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
        {{ request()->routeIs('vehicle-price-origins.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="bank" class="w-3.5 h-3.5"/>
                        <span>Patrimônios</span>
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('vehicle-transfers.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicle-transfers.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="shuffle" class="w-3.5 h-3.5"/>
                    <span>Transferências</span>
                </a>
            </li>
            @if(auth()->user()->isManager())
                <li>
                    <a href="{{ route('vehicles.usage-panel') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
            {{ request()->routeIs('vehicles.usage-panel') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="bar-chart-line" class="w-3.5 h-3.5"/>
                        <span>Veículos em Uso</span>
                    </a>
                </li>
            @endif
        </ul>

    </li>
    <!-- Manutenção -->
    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($maintenanceGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                      aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ maintenanceSubmenuOpen = !maintenanceSubmenuOpen; } else { maintenanceOpen = !maintenanceOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ maintenanceSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $maintenanceGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="box-seam" class="w-5 h-5 shrink-0"/>
                <span class="truncate flex-1 text-left"
                      x-show="!isSidebarCollapsed || isMobileSidebarOpen">Manutenção</span>
                <x-icon name="chevron-down" id="nav-maintenance-chevron"
                        x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                        x-bind:class="maintenanceOpen ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"/>
            </button>
            <ul id="nav-maintenance-submenu" x-show="maintenanceOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
                class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <!-- Troca de Óleo -->
                <li>
                    <a href="{{ route('oil-changes.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('oil-changes.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="moisture" class="w-3.5 h-3.5 shrink-0"/>
                        <span>Troca de Óleo</span>
                    </a>
                </li>
                @if(auth()->user()->isGeneralManager())
                    <li>
                        <a href="{{ route('oil-changes.settings') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('oil-changes.settings') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="wrench" class="w-3.5 h-3.5"/>
                            <span>Configurações Óleo</span>
                        </a>
                    </li>
                @endif
                <!-- Troca de Pneus -->
                <li>
                    <a href="{{ route('tires.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="vinyl" class="w-3.5 h-3.5"/>
                        <span>Dashboard Pneus</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tires.vehicles') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.vehicles') || request()->routeIs('tires.vehicles.show') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="truck" class="w-3.5 h-3.5"/>
                        <span>Veículos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tires.stock') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.stock') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="box-seam" class="w-3.5 h-3.5"/>
                        <span>Estoque</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tires.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('tires.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus-circle" class="w-3.5 h-3.5"/>
                        <span>Cadastrar Pneu</span>
                    </a>
                </li>
            </ul>

        </li>
    @endif
    <!-- Combustível -->
    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($fuelGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                      aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ fuelSubmenuOpen = !fuelSubmenuOpen; } else { fuelOpen = !fuelOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ fuelSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
        {{ $fuelGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="fuel-pump" class="w-5 h-5 shrink-0"/>
                <span class="truncate flex-1 text-left"
                      x-show="!isSidebarCollapsed || isMobileSidebarOpen">Combustível</span>
                <x-icon name="chevron-down" id="nav-fuel-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                        x-bind:class="fuelOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
            </button>
            <ul id="nav-fuel-submenu" x-show="fuelOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
                class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                {{-- Links existentes --}}
                <li>
                    <a href="{{ route('gas-stations.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('gas-stations.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="fuel-pump" class="w-3.5 h-3.5"/>
                        <span>Postos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('gas_stations_current.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('gas_stations_current.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="calendar-check" class="w-3.5 h-3.5"/>
                        <span>Postos Ativos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('scheduled_gas_stations.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('scheduled_gas_stations.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="calendar2-plus" class="w-3.5 h-3.5"/>
                        <span>Agendamentos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('fuel_prices.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel_prices.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="cash-stack" class="w-3.5 h-3.5"/>
                        <span>Preços Atuais</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('scheduled_prices.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('scheduled_prices.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="cash-coin" class="w-3.5 h-3.5"/>
                        <span>Agendamento de Preços</span>
                    </a>
                </li>

                {{-- Links existentes --}}
                <li>
                    <a href="{{ route('fuel-quotations.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel-quotations.index') || request()->routeIs('fuel-quotations.show') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="card-checklist" class="w-3.5 h-3.5"/>
                        <span>Cotações</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('fuel-quotations.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel-quotations.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="plus-circle" class="w-3.5 h-3.5"/>
                        <span>Nova Cotação</span>
                    </a>
                </li>
                @if(auth()->user()->isGeneralManager())
                    <li>
                        <a href="{{ route('fuel-quotations.settings') }}"
                           class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fuel-quotations.settings') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="wrench" class="w-3.5 h-3.5"/>
                            <span>Configurações</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasRole('general_manager') || auth()->user()->hasRole('sector_manager'))
                    <li>
                        <a href="{{ route('fueling_expenses.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fueling_expenses.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="coin" class="w-3.5 h-3.5"/>
                            <span>Despesas</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($finesGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                      aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ finesSubmenuOpen = !finesSubmenuOpen; } else { finesOpen = !finesOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ finesSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
        {{ $finesGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="person-video2" class="w-5 h-5 shrink-0"/>
                <span class="truncate flex-1 text-left"
                      x-show="!isSidebarCollapsed || isMobileSidebarOpen">Multas</span>
                <x-icon name="chevron-down" id="nav-fines-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                        x-bind:class="finesOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
            </button>
            <ul id="nav-fines-submenu" x-show="finesOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
                class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <li>
                    <a href="{{ route('fines.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fines.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="clipboard-data" class="w-3.5 h-3.5"/>
                        <span>Gerenciar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('fines.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                {{ request()->routeIs('fines.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="inboxes" class="w-3.5 h-3.5"/>
                        <span>Cadastrar</span>
                    </a>
                </li>
            </ul>
            <div x-cloak
                 x-show="finesSubmenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
                 x-transition
                 class="absolute left-full top-0 ml-2 w-56 bg-white dark:bg-navy-800 rounded-lg shadow-xl border border-gray-200 dark:border-navy-700 py-2 z-50">
                <div
                    class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-navy-300 uppercase tracking-wider border-b border-gray-200 dark:border-navy-700 mb-1">
                    Multas
                </div>
                <a href="{{ route('fines.index') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('fines.index') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="list" class="w-4 h-4"/>
                    <span>Gerenciar</span>
                </a>
                <a href="{{ route('fines.create') }}"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition {{ request()->routeIs('fines.create') ? 'bg-primary-50 dark:bg-navy-700 text-primary-700 dark:text-navy-50' : '' }}">
                    <x-icon name="plus" class="w-4 h-4"/>
                    <span>Cadastrar</span>
                </a>
            </div>
        </li>
    @endif
    <!-- Relatórios / Backups -->
    <li class="relative group">
        @if($reportsGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                  aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ reportsSubmenuOpen = !reportsSubmenuOpen; } else { reportsOpen = !reportsOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ reportsSubmenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $reportsGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="file-earmark-text" class="w-5 h-5 shrink-0"/>
            <span class="truncate flex-1 text-left"
                  x-show="!isSidebarCollapsed || isMobileSidebarOpen">Relatórios</span>
            <x-icon name="chevron-down" id="nav-reports-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                    x-bind:class="reportsOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
        </button>
        <ul id="nav-reports-submenu" x-show="reportsOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
            class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
            <li>
                <a href="{{ route('backup-reports.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('backup-reports.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="file-earmark-pdf" class="w-3.5 h-3.5"/>
                    <span>Backups</span>
                </a>
            </li>
            @if(auth()->user()->isGeneralManager())
                <li>
                    <a href="{{ route('pdf-templates.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('pdf-templates.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="file-earmark-richtext" class="w-3.5 h-3.5"/>
                        <span>Modelos</span>
                    </a>
                </li>
            @endif
        </ul>

    </li>
    <!-- Usuários -->
    @if(auth()->user()->isManager())
        <li class="relative group">
            @if($usersGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                      aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ usersSubmenuOpen = !usersSubmenuOpen; } else { usersOpen = !usersOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ usersSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $usersGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="users" class="w-5 h-5 shrink-0"/>
                <span class="truncate flex-1 text-left"
                      x-show="!isSidebarCollapsed || isMobileSidebarOpen">Usuários</span>
                <x-icon name="chevron-down" id="nav-users-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                        x-bind:class="usersOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
            </button>
            <ul id="nav-users-submenu" x-show="usersOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
                class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <li>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('users.*') && !request()->routeIs('users.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="person-vcard" class="w-3.5 h-3.5"/>
                        <span>Gerenciar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.create') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('users.create') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="person-add" class="w-3.5 h-3.5"/>
                        <span>Cadastrar</span>
                    </a>
                </li>
                @if(auth()->user()->isGeneralManager())
                    <li>
                        <a href="{{ route('default-passwords.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('default-passwords.*') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                            <x-icon name="key" class="w-3.5 h-3.5"/>
                            <span>Senhas Padrão</span>
                        </a>
                    </li>
                @endif
            </ul>

        </li>
    @endif
    <!-- Auditoria -->
    @if(auth()->user()->isGeneralManager())
        <li class="relative group">
            @if($auditGroupActive)
                <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                      aria-hidden="true"></span>
            @endif
            <button type="button"
                    @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ auditSubmenuOpen = !auditSubmenuOpen; } else { auditOpen = !auditOpen; }"
                    @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ auditSubmenuOpen = false; }"
                    class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $auditGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
                <x-icon name="archive" class="w-5 h-5 shrink-0"/>
                <span class="truncate flex-1 text-left"
                      x-show="!isSidebarCollapsed || isMobileSidebarOpen">Auditoria</span>
                <x-icon name="chevron-down" id="nav-audit-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen"
                        x-bind:class="auditOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200"/>
            </button>
            <ul id="nav-audit-submenu" x-show="auditOpen && (!isSidebarCollapsed || isMobileSidebarOpen)"
                class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600 submenu-transition">
                <li>
                    <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('audit-logs.index') && !request()->input('type') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="book" class="w-3.5 h-3.5"/>
                        <span>Todos os Logs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('audit-logs.index', ['type' => 'App\Models\user\User']) }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->input('type') == 'App\Models\User' ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="person-gear" class="w-3.5 h-3.5"/>
                        <span>Usuários</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('audit-logs.index', ['type' => 'App\Models\Vehicle\Vehicle']) }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->input('type') == 'App\Models\Vehicle' ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="truck" class="w-3.5 h-3.5"/>
                        <span>Veículos</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('audit-logs.index', ['type' => 'App\Models\run\Run']) }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->input('type') == 'App\Models\Run' ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                        <x-icon name="arrow-left-right" class="w-3.5 h-3.5"/>
                        <span>Rodagens</span>
                    </a>
                </li>
            </ul>

        </li>

    @endif

    <li class="relative group">
        @if(request()->routeIs('chat.*'))
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg"
                  aria-hidden="true"></span>
        @endif
        <a href="{{ route('chat.index') }}"
           class="flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200
                  {{ request()->routeIs('chat.*') ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="chat-bubble" class="w-3.5 h-3.5"/>
            <span class="truncate" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Chat</span>
            <span x-cloak x-show="isSidebarCollapsed && !isMobileSidebarOpen"
                  class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-primary-600 text-white text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow z-50">Chat</span>
        </a>
    </li>
</ul>
