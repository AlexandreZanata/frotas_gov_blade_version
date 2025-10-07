<header class="z-10 py-4 bg-white shadow-md dark:bg-gray-800">
    <div class="container flex items-center justify-between h-full px-6 mx-auto">

        <div class="flex items-center">
            <button @click="isSidebarOpen = !isSidebarOpen"
                    class="hidden p-1 rounded-md md:block focus:outline-none focus:shadow-outline-red text-gray-500 hover:text-red-600"
                    aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
        </div>

        <ul class="flex items-center flex-shrink-0 space-x-4">
            <li class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="align-middle rounded-full focus:shadow-outline-red focus:outline-none"
                        aria-label="Account"
                        aria-haspopup="true">
                    <img class="object-cover w-8 h-8 rounded-full" src="https://via.placeholder.com/40" alt="User" aria-hidden="true" />
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 w-56 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700"
                     style="display: none;">

                    {{-- Formulário de Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();"
                           class="flex items-center w-full px-4 py-2 text-sm font-medium leading-5 text-left text-gray-700 transition-colors duration-150 rounded-md dark:text-gray-300 hover:bg-red-100 hover:text-red-800 dark:hover:bg-red-700 dark:hover:text-red-100">
                            <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Sair</span>
                        </a>
                    </form>
                </div>
            </li>

            <li class="md:hidden">
                <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="p-1 rounded-md focus:outline-none focus:shadow-outline-red text-gray-500 hover:text-red-600"
                        aria-label="Menu">
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</header>
