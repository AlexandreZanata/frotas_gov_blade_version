{{-- Links de Navegação Sidebar --}}
@php($vehicleGroupActive = request()->routeIs('vehicles.*') || request()->routeIs('vehicle-categories.*') || request()->routeIs('prefixes.*'))
@php($logbookGroupActive = request()->routeIs('logbook.*') || request()->routeIs('logbook-permissions.*'))
@php($reportsGroupActive = request()->routeIs('backup-reports.*') || request()->routeIs('pdf-templates.*'))
@php($usersGroupActive = request()->routeIs('users.*') || request()->routeIs('default-passwords.*'))
@php($auditGroupActive = request()->routeIs('audit-logs.*'))

{{-- CSS inline para aplicar estado ANTES do Alpine.js carregar - SOLUÇÃO DEFINITIVA --}}
<style>
    /* Aplicar estado inicial via CSS puro - renderizado no servidor */
    @if($vehicleGroupActive)
        #nav-vehicles-submenu { display: block !important; }
        #nav-vehicles-chevron { transform: rotate(180deg); }
    @endif

    @if($logbookGroupActive)
        #nav-logbook-submenu { display: block !important; }
        #nav-logbook-chevron { transform: rotate(180deg); }
    @endif

    @if($reportsGroupActive)
        #nav-reports-submenu { display: block !important; }
        #nav-reports-chevron { transform: rotate(180deg); }
    @endif

    @if($usersGroupActive)
        #nav-users-submenu { display: block !important; }
        #nav-users-chevron { transform: rotate(180deg); }
    @endif

    @if($auditGroupActive)
        #nav-audit-submenu { display: block !important; }
        #nav-audit-chevron { transform: rotate(180deg); }
    @endif

    /* Esconder elementos x-cloak ANTES do Alpine carregar */
    [x-cloak] { display: none !important; }
</style>

<ul class="mt-4 space-y-1"
    x-data="{
        vehiclesOpen: {{ $vehicleGroupActive ? 'true' : 'false' }},
        logbookOpen: {{ $logbookGroupActive ? 'true' : 'false' }},
        reportsOpen: {{ $reportsGroupActive ? 'true' : 'false' }},
        usersOpen: {{ $usersGroupActive ? 'true' : 'false' }},
        auditOpen: {{ $auditGroupActive ? 'true' : 'false' }}
    }"
    x-init="
    @if(!$vehicleGroupActive)
        if (localStorage.getItem('nav-vehicles-open') !== null) {
            vehiclesOpen = localStorage.getItem('nav-vehicles-open') === 'true';
        }
    @endif

    @if(!$logbookGroupActive)
        if (localStorage.getItem('nav-logbook-open') !== null) {
            logbookOpen = localStorage.getItem('nav-logbook-open') === 'true';
        }
    @endif

    @if(!$reportsGroupActive)
        if (localStorage.getItem('nav-reports-open') !== null) {
            reportsOpen = localStorage.getItem('nav-reports-open') === 'true';
        }
    @endif

    @if(!$usersGroupActive)
        if (localStorage.getItem('nav-users-open') !== null) {
            usersOpen = localStorage.getItem('nav-users-open') === 'true';
        }
    @endif

    @if(!$auditGroupActive)
        if (localStorage.getItem('nav-audit-open') !== null) {
            auditOpen = localStorage.getItem('nav-audit-open') === 'true';
        }
    @endif

    $watch('vehiclesOpen', value => { if (!{{ $vehicleGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-vehicles-open', value); });
    $watch('logbookOpen', value => { if (!{{ $logbookGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-logbook-open', value); });
    $watch('reportsOpen', value => { if (!{{ $reportsGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-reports-open', value); });
    $watch('usersOpen', value => { if (!{{ $usersGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-users-open', value); });
    $watch('auditOpen', value => { if (!{{ $auditGroupActive ? 'true' : 'false' }}) localStorage.setItem('nav-audit-open', value); });
">
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
    <li class="relative group" x-data="{ submenuOpen: false }">
        @if($logbookGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = !submenuOpen; } else { logbookOpen = !logbookOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $logbookGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="clipboard" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Diário de Bordo</span>
            <x-icon name="chevron-down" id="nav-logbook-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="logbookOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>

        <ul id="nav-logbook-submenu" x-show="logbookOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600" style="display: none;">
            <li>
                <a href="{{ route('logbook.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="list" class="w-3.5 h-3.5" /> <span>Minhas Corridas</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logbook.start-flow') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('logbook.start-flow') || request()->routeIs('logbook.vehicle-select') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <x-icon name="plus" class="w-3.5 h-3.5" /> <span>Nova Corrida</span>
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
        </ul>

        <!-- Submenu popup quando colapsada -->
        <div x-cloak
             x-show="submenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
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

    <!-- Grupo Veículos -->
    <li class="relative group" x-data="{ submenuOpen: false }">
        @if($vehicleGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = !submenuOpen; } else { vehiclesOpen = !vehiclesOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $vehicleGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="car" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Veículos</span>
            <x-icon name="chevron-down" id="nav-vehicles-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="vehiclesOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>

        <ul id="nav-vehicles-submenu" x-show="vehiclesOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600" style="display: none;">
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
        </ul>

        <!-- Submenu popup quando colapsada -->
        <div x-cloak
             x-show="submenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
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
        </div>
    </li>

    <!-- Relatórios / Backups -->
    <li class="relative group" x-data="{ submenuOpen: false }">
        @if($reportsGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = !submenuOpen; } else { reportsOpen = !reportsOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $reportsGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="document" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Relatórios</span>
            <x-icon name="chevron-down" id="nav-reports-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="reportsOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>

        <ul id="nav-reports-submenu" x-show="reportsOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600" style="display: none;">
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
             x-show="submenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
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
    <li class="relative group" x-data="{ submenuOpen: false }">
        @if($usersGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = !submenuOpen; } else { usersOpen = !usersOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $usersGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="users" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Usuários</span>
            <x-icon name="chevron-down" id="nav-users-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="usersOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>

        <ul id="nav-users-submenu" x-show="usersOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600" style="display: none;">
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
             x-show="submenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
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
    <li class="relative group" x-data="{ submenuOpen: false }">
        @if($auditGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = !submenuOpen; } else { auditOpen = !auditOpen; }"
                @click.away="if(isSidebarCollapsed && !isMobileSidebarOpen){ submenuOpen = false; }"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $auditGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="clipboard" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen">Auditoria</span>
            <x-icon name="chevron-down" id="nav-audit-chevron" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="auditOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
        </button>

        <ul id="nav-audit-submenu" x-show="auditOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600" style="display: none;">
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
             x-show="submenuOpen && isSidebarCollapsed && !isMobileSidebarOpen"
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
</ul>
