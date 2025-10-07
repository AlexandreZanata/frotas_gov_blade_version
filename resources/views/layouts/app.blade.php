<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="layoutState()" x-init="init()" x-bind:class="{ 'dark': darkMode }" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <script>/* Anti-flicker dark mode early apply */(function(){try{const s=localStorage.getItem('theme-dark');if(s==='true'||(s===null&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}}catch(e){}})();</script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <style>[x-cloak]{display:none!important} .no-transition *{transition:none!important}</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            function layoutState() {
                return {
                    isSidebarCollapsed: false,
                    isMobileSidebarOpen: false,
                    darkMode: false,
                    pageLoading: false,
                    init() {
                        const collapsedStored = localStorage.getItem('sidebar-collapsed');
                        if (collapsedStored !== null) this.isSidebarCollapsed = collapsedStored === 'true';
                        const stored = localStorage.getItem('theme-dark');
                        if (stored !== null) this.darkMode = stored === 'true'; else this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e)=>{ if(!localStorage.getItem('theme-dark')) this.darkMode=e.matches; });
                        this.setupPageTransitions();
                    },
                    toggleCollapse(){ this.isSidebarCollapsed=!this.isSidebarCollapsed; localStorage.setItem('sidebar-collapsed', this.isSidebarCollapsed);},
                    openMobile(){ this.isMobileSidebarOpen=true; },
                    closeMobile(){ this.isMobileSidebarOpen=false; },
                    toggleDark(){ this.darkMode=!this.darkMode; localStorage.setItem('theme-dark', this.darkMode);},
                    setupPageTransitions(){
                        const self=this;
                        const show=()=>{ self.pageLoading=true; };
                        const hide=()=>{ self.pageLoading=false; };
                        window.addEventListener('pageshow', hide);
                        document.addEventListener('alpine:init', hide);
                        document.addEventListener('submit', e=>{ const f=e.target; if(!f.target||f.target==='_self'){ show(); }});
                        document.addEventListener('click', e=>{
                            const a=e.target.closest('a');
                            if(!a) return; if(a.hasAttribute('data-no-progress')) return;
                            const url=a.getAttribute('href'); if(!url || url.startsWith('#') || a.target==='_blank' || url.startsWith('mailto:') || url.startsWith('tel:')) return;
                            if(a.dataset?.turbo==='false'){} // placeholder
                            show();
                        });
                    }
                }
            }
        </script>
    </head>
    <body class="font-sans antialiased h-full bg-gray-100 dark:bg-navy-900">
        <!-- Page Loading Overlay -->
        <div x-cloak x-show="pageLoading" x-transition.opacity class="fixed inset-0 z-[999] flex flex-col items-center justify-center bg-white/70 dark:bg-navy-900/90 backdrop-blur-sm">
            <div class="relative">
                <div class="h-14 w-14 rounded-full border-4 border-primary-200 dark:border-navy-600 border-t-primary-600 dark:border-t-navy-200 animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-7 h-7 text-primary-600 dark:text-navy-100 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2l1 3h12l1-3h2M5 13l4-8h6l4 8M10 9h4" /></svg>
                </div>
            </div>
            <p class="mt-6 text-sm font-medium text-primary-700 dark:text-navy-100 tracking-wide">Carregando...</p>
        </div>
        <!-- Sidebar Desktop & Mobile Offcanvas -->
        @include('layouts.sidebar')

        <!-- Main Wrapper ajusta margem conforme sidebar -->
        <div class="min-h-screen flex flex-col transition-[margin] duration-300 ease-in-out" x-bind:class="isSidebarCollapsed ? 'lg:ml-16' : 'lg:ml-64'">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 h-16 flex items-center gap-3 px-4 lg:px-6 bg-white/85 dark:bg-navy-800/90 backdrop-blur border-b border-gray-200 dark:border-navy-700 shadow-sm">
                <!-- User Dropdown (mobile esquerda, desktop direita) -->
                <div class="relative order-1 lg:order-5" x-data="{ open:false }">
                    <button @click="open=!open" class="inline-flex items-center gap-2 pl-3 pr-3 h-10 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition">
                        <span class="hidden sm:inline-block max-w-[8rem] truncate">{{ Auth::user()->name }}</span>
                        <x-icon name="chevron-down" class="h-4 w-4 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.away="open=false" x-transition.origin-top-left class="absolute left-0 lg:left-auto lg:right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-700 py-1 z-20 overflow-hidden">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-navy-50 hover:bg-primary-50 dark:hover:bg-navy-700/60">Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-navy-50 hover:bg-primary-50 dark:hover:bg-navy-700/60">Sair</button>
                        </form>
                    </div>
                </div>
                <!-- Collapse desktop -->
                <button @click="toggleCollapse" aria-controls="sidebar" x-bind:aria-expanded="!isSidebarCollapsed" class="hidden lg:inline-flex order-1 lg:order-1 items-center justify-center h-9 w-9 rounded-md text-gray-500 dark:text-navy-100 hover:text-primary-600 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-navy-700/60 transition" x-bind:title="isSidebarCollapsed ? 'Expandir sidebar' : 'Recolher sidebar'">
                    <x-icon name="chevron-left" x-show="!isSidebarCollapsed" class="h-5 w-5" />
                    <x-icon name="chevron-right" x-show="isSidebarCollapsed" x-cloak class="h-5 w-5" />
                </button>
                <!-- Título -->
                <div class="flex-1 flex items-center min-w-0 order-2 lg:order-2">
                    @isset($header)
                        <div class="text-gray-700 dark:text-navy-50 font-semibold truncate">{{ $header }}</div>
                    @endisset
                </div>
                <!-- Toggle Dark Mode -->
                <button @click="toggleDark" class="order-3 lg:order-3 inline-flex items-center justify-center h-10 w-10 rounded-md text-primary-600 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition" x-bind:title="darkMode ? 'Modo claro' : 'Modo escuro'">
                    <x-icon name="sun" x-show="!darkMode" class="h-5 w-5" />
                    <x-icon name="moon" x-show="darkMode" x-cloak class="h-5 w-5" />
                </button>
                <!-- Hamburger agora à direita somente mobile -->
                <button @click="openMobile" aria-controls="mobile-sidebar" x-bind:aria-expanded="isMobileSidebarOpen" class="order-4 lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-md text-primary-600 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition" aria-label="Abrir menu">
                    <x-icon name="menu" x-show="!isMobileSidebarOpen" class="h-6 w-6" />
                    <x-icon name="close" x-show="isMobileSidebarOpen" x-cloak class="h-6 w-6" />
                </button>
            </header>

            <!-- Page Content -->
            <main class="p-4 lg:p-6 flex-1 space-y-6">
                <x-ui.flash />
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
