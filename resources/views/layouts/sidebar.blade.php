{{-- Nova Sidebar Interativa (paleta corporativa) --}}
<aside
    id="sidebar"
    class="hidden lg:flex flex-col h-screen fixed top-0 left-0 z-40 border-r border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800 bg-gradient-to-b from-white to-primary-50/40 dark:from-navy-900 dark:to-navy-800 transition-all duration-300 ease-in-out shadow-sm"
    x-bind:class="isSidebarCollapsed ? 'w-16' : 'w-64'"
    x-cloak
>
    <!-- Top (Brand + Collapse) -->
    <div class="flex items-center h-16 px-4 gap-2 border-b border-gray-200 dark:border-navy-700">
        <div class="flex items-center gap-2 overflow-hidden select-none">
            <span class="font-semibold tracking-wide text-primary-700 dark:text-navy-50" x-show="!isSidebarCollapsed" x-transition.opacity>Frotas Gov</span>
        </div>
        <button @click="toggleCollapse" class="ml-auto flex items-center justify-center h-8 w-8 rounded-md text-gray-500 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 hover:text-primary-700 dark:hover:text-white transition" :title="isSidebarCollapsed ? 'Expandir' : 'Recolher'">
            <svg x-show="!isSidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            <svg x-show="isSidebarCollapsed" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <!-- Scrollable Nav (sem scroll horizontal quando colapsada) -->
    <nav class="flex-1 px-2"
         x-data
         x-ref="sidebarNav"
         x-bind:class="isSidebarCollapsed ? 'overflow-y-hidden hover:overflow-y-auto' : 'overflow-y-auto'"
         x-init="(() => { const k='sidebar-scroll'; requestAnimationFrame(()=>{ $refs.sidebarNav.scrollTop = parseInt(localStorage.getItem(k)||0); }); $refs.sidebarNav.addEventListener('scroll', () => localStorage.setItem(k, $refs.sidebarNav.scrollTop), {passive:true}); })()">
        @include('layouts.navigation-links')
    </nav>

    <!-- Footer (User quick info) -->
    <div class="p-3 border-t border-gray-200 dark:border-navy-700" x-show="!isSidebarCollapsed" x-transition.opacity>
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-primary-100 dark:bg-navy-700 flex items-center justify-center text-primary-700 dark:text-white font-semibold">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="text-xs leading-tight">
                <div class="font-semibold text-gray-700 dark:text-navy-50 truncate">{{ Auth::user()->name }}</div>
                <div class="text-gray-500 dark:text-navy-200 truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar (Right Side) -->
<div x-cloak x-show="isMobileSidebarOpen" class="lg:hidden">
    <div @click="closeMobile" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"></div>
    <aside
        id="mobile-sidebar"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 z-50 w-72 h-screen flex flex-col bg-white dark:bg-navy-800 border-l border-gray-200 dark:border-navy-700 shadow-xl"
    >
        <div class="flex items-center h-16 px-4 gap-2 border-b border-gray-200 dark:border-navy-700">
            <span class="font-semibold text-lg text-gray-700 dark:text-navy-50">Menu</span>
            <button @click="closeMobile" class="ml-auto flex items-center justify-center h-8 w-8 rounded-md text-gray-500 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 hover:text-primary-700 dark:hover:text-white transition" title="Fechar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto px-2 py-2">
            @include('layouts.navigation-links')
        </nav>
    </aside>
</div>
