<nav x-data="{ open: false }" class="bg-white dark:bg-navy-800 border-b border-gray-100 dark:border-navy-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-primary-600 dark:text-navy-100 font-semibold tracking-wide text-lg select-none">
                        {{ config('app.name','Frotas') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="[&.active]:text-primary-700 dark:[&.active]:text-navy-50">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Dropdown de Manutenção -->
                    <div class="relative" x-data="{ openMaintenance: false }" @click.away="openMaintenance = false">
                        <button @click="openMaintenance = !openMaintenance"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('tires.*') || request()->routeIs('oil-changes.*') ? 'border-primary-700 text-gray-900 dark:text-navy-50 focus:border-primary-700' : 'border-transparent text-gray-500 dark:text-navy-200 hover:text-gray-700 dark:hover:text-navy-50 hover:border-gray-300 dark:hover:border-navy-600 focus:text-gray-700 dark:focus:text-navy-50 focus:border-gray-300 dark:focus:border-navy-600' }}">
                            <i class="fas fa-tools mr-1"></i> {{ __('Manutenção') }}
                            <svg class="ml-1 h-4 w-4" :class="{'rotate-180': openMaintenance}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="openMaintenance"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-50 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-navy-800 ring-1 ring-black ring-opacity-5"
                             style="display: none;">
                            <div class="py-1" role="menu">
                                <a href="{{ route('tires.index') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-navy-200 hover:bg-gray-100 dark:hover:bg-navy-700 {{ request()->routeIs('tires.*') ? 'bg-gray-100 dark:bg-navy-700' : '' }}"
                                   role="menuitem">
                                    <i class="fas fa-circle-notch mr-2"></i> Troca de Pneus
                                </a>
                                <a href="{{ route('oil-changes.index') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-navy-200 hover:bg-gray-100 dark:hover:bg-navy-700 {{ request()->routeIs('oil-changes.*') ? 'bg-gray-100 dark:bg-navy-700' : '' }}"
                                   role="menuitem">
                                    <i class="fas fa-oil-can mr-2"></i> Troca de Óleo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-navy-200 bg-white dark:bg-navy-800 hover:text-gray-700 dark:hover:text-navy-50 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-navy-300 hover:text-gray-500 dark:hover:text-navy-100 hover:bg-gray-100 dark:hover:bg-navy-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-navy-700 focus:text-gray-500 dark:focus:text-navy-100 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Manutenção - Mobile -->
            <div class="border-t border-gray-200 dark:border-navy-700 mt-2 pt-2">
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-navy-400 uppercase">
                    Manutenção
                </div>
                <x-responsive-nav-link :href="route('tires.index')" :active="request()->routeIs('tires.*')">
                    <i class="fas fa-circle-notch mr-2"></i> {{ __('Troca de Pneus') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('oil-changes.index')" :active="request()->routeIs('oil-changes.*')">
                    <i class="fas fa-oil-can mr-2"></i> {{ __('Troca de Óleo') }}
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-navy-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-navy-50">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-navy-200">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
