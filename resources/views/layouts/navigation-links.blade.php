{{-- Links de Navegação Sidebar --}}
@php($vehicleGroupActive = request()->routeIs('vehicles.*') || request()->routeIs('vehicle-categories.*') || request()->routeIs('prefixes.*'))
<ul class="mt-4 space-y-1" x-data="{
    vehiclesOpen: (()=>{const s=localStorage.getItem('nav-vehicles-open'); return s===null ? {{ $vehicleGroupActive? 'true':'false' }} : (s==='true');})()
}">
    <!-- Dashboard -->
    <li class="relative group">
        @if(request()->routeIs('dashboard'))
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200
                  {{ request()->routeIs('dashboard') ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="dashboard" class="w-5 h-5 shrink-0" />
            <span class="truncate" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-transition.opacity>Dashboard</span>
            <!-- Tooltip quando colapsado -->
            <span x-cloak x-show="isSidebarCollapsed && !isMobileSidebarOpen" class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-primary-600 text-white text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow">Dashboard</span>
        </a>
    </li>

    <!-- Grupo Veículos -->
    <li class="relative group" x-data>
        @if($vehicleGroupActive)
            <span class="absolute inset-y-0 left-0 w-1 bg-primary-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
        @endif
        <button type="button"
                @click="vehiclesOpen = !vehiclesOpen; localStorage.setItem('nav-vehicles-open', vehiclesOpen)"
                class="w-full flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition-colors duration-200 focus:outline-none
            {{ $vehicleGroupActive ? 'text-primary-700 dark:text-navy-50 bg-primary-50 dark:bg-navy-700/50' : 'text-gray-600 dark:text-navy-100 hover:text-primary-700 hover:bg-primary-50 dark:hover:text-white dark:hover:bg-navy-700/40' }}">
            <x-icon name="car" class="w-5 h-5 shrink-0" />
            <span class="truncate flex-1 text-left" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-transition.opacity>Veículos</span>
            <x-icon name="chevron-down" x-show="!isSidebarCollapsed || isMobileSidebarOpen" x-bind:class="vehiclesOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" />
            <span x-cloak x-show="isSidebarCollapsed && !isMobileSidebarOpen" class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-primary-600 text-white text-xs opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow">Veículos</span>
        </button>
        <ul x-cloak x-show="vehiclesOpen && (!isSidebarCollapsed || isMobileSidebarOpen)" x-transition.opacity class="mt-1 pl-3 pr-1 space-y-1 border-l border-gray-200 dark:border-navy-600">
            <li>
                <a href="{{ route('vehicles.index') }}" class="flex items-center gap-2 rounded-md px-3 py-1.5 text-xs font-medium tracking-wide transition-colors duration-150
                    {{ request()->routeIs('vehicles.index') ? 'bg-primary-100 text-primary-700 dark:bg-navy-700 dark:text-navy-50' : 'text-gray-600 dark:text-navy-100 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-navy-700/60 dark:hover:text-white' }}">
                    <span>Gerenciar</span>
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
    </li>
</ul>
