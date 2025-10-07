{{-- O container foi removido para evitar div extra, as sidebars agora são irmãs do conteúdo principal --}}

<div x-show="isMobileMenuOpen"
     x-transition:enter="transition ease-in-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in-out duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-20 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
     @click="isMobileMenuOpen = false">
</div>

<aside x-show="isMobileMenuOpen"
       x-transition:enter="transition ease-in-out duration-300 transform"
       x-transition:enter-start="translate-x-full" {{-- Mudou de -translate-x-full --}}
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full" {{-- Mudou de -translate-x-full --}}
       @keydown.escape.window="isMobileMenuOpen = false"
       class="fixed inset-y-0 right-0 z-30 flex-shrink-0 w-64 overflow-y-auto bg-white dark:bg-gray-800 md:hidden"> {{-- Mudou de 'left-0' para 'right-0' --}}

    <div class="py-4 text-gray-500 dark:text-gray-400">
        <a class="ml-6 text-lg font-bold text-gray-800 dark:text-gray-200" href="#">
            Frotas Gov
        </a>
        @include('layouts.navigation-links')
    </div>
</aside>

<aside class="z-30 flex-shrink-0 hidden h-full overflow-y-auto bg-white border-r dark:bg-gray-800 dark:border-gray-700 md:flex md:flex-col transition-all duration-300"
       :class="isSidebarOpen ? 'w-64' : 'w-20'">

    <div class="py-4 text-gray-500 dark:text-gray-400 flex-grow">
        {{-- ... (conteúdo da sidebar de desktop não mudou) ... --}}
        <a class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center justify-center" href="{{ route('dashboard') }}">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span x-show="isSidebarOpen" class="ml-2 whitespace-nowrap">Frotas Gov</span>
        </a>

        @include('layouts.navigation-links')
    </div>

    {{-- O botão de recolher foi movido para a navbar para uma UI mais limpa --}}
</aside>
