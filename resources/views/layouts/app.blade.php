<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="layoutState()" x-init="init();" x-bind:class="{ 'dark': darkMode }" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Anti-flicker scripts melhorados -->
    <script>
        (function(){
            try {
                var d = document.documentElement;
                var s = localStorage.getItem('theme-dark');

                // Aplicar tema dark imediatamente
                if(s === 'true' || (s === null && window.matchMedia('(prefers-color-scheme: dark)').matches)){
                    d.classList.add('dark');
                }

                // Aplicar estado da sidebar imediatamente
                var collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                d.classList.add('pre-render');
                d.style.setProperty('--sidebar-w', collapsed ? '4rem' : '16rem');

                // Remover classes após renderização
                setTimeout(() => {
                    d.classList.remove('pre-render');
                }, 100);
            } catch(e) {}
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        :root { --sidebar-w: 16rem; }

        .layout-shell {
            min-height: 100vh;
        }

        @media (min-width: 1024px) {
            .layout-shell {
                margin-left: var(--sidebar-w);
                transition: margin-left .3s ease-in-out;
            }
        }

        .pre-render .layout-shell {
            margin-left: var(--sidebar-w);
        }

        @media (max-width: 1023px){
            .pre-render .layout-shell {
                margin-left: 0 !important;
            }
        }

        [x-cloak] {
            display: none !important;
        }

        .pre-render * {
            transition: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        function layoutState() {
            return {
                isSidebarCollapsed: (localStorage.getItem('sidebar-collapsed') === 'true'),
                isMobileSidebarOpen: false,
                darkMode: false,
                pageLoading: false,

                init() {
                    // Configurar tema
                    const stored = localStorage.getItem('theme-dark');
                    if (stored !== null) {
                        this.darkMode = stored === 'true';
                    } else {
                        this.darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    }

                    // Listener para mudanças de tema do sistema
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                        if(!localStorage.getItem('theme-dark')) {
                            this.darkMode = e.matches;
                        }
                    });

                    this.setupPageTransitions();
                },

                toggleCollapse() {
                    this.isSidebarCollapsed = !this.isSidebarCollapsed;
                    localStorage.setItem('sidebar-collapsed', this.isSidebarCollapsed);
                    document.documentElement.style.setProperty('--sidebar-w', this.isSidebarCollapsed ? '4rem' : '16rem');
                },

                openMobile() {
                    this.isMobileSidebarOpen = true;
                },

                closeMobile() {
                    this.isMobileSidebarOpen = false;
                },

                toggleDark() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('theme-dark', this.darkMode);
                },

                setupPageTransitions() {
                    const self = this;
                    const show = () => { self.pageLoading = true; };
                    const hide = () => { self.pageLoading = false; };

                    window.addEventListener('pageshow', hide);
                    document.addEventListener('alpine:init', hide);

                    document.addEventListener('submit', e => {
                        const f = e.target;
                        if(!f.target || f.target === '_self') {
                            show();
                        }
                    });

                    document.addEventListener('click', e => {
                        const a = e.target.closest('a');
                        if(!a) return;
                        if(a.hasAttribute('data-no-progress')) return;

                        const url = a.getAttribute('href');
                        if(!url || url.startsWith('#') || a.target === '_blank' ||
                            url.startsWith('mailto:') || url.startsWith('tel:')) return;

                        show();
                    });
                }
            }
        }
    </script>
</head>

<body class="font-sans antialiased h-full bg-gray-100 dark:bg-navy-900">
<!-- Page Loading Overlay -->
<div x-cloak x-show="pageLoading" x-transition.opacity class="fixed inset-0 z-[999] flex flex-col items-center justify-center bg-gradient-to-br from-white via-primary-50/30 to-primary-100/40 dark:from-navy-900 dark:via-navy-900/95 dark:to-navy-800/90 backdrop-blur-sm">
    <div class="relative">
        <!-- Círculo externo pulsante -->
        <div class="absolute inset-0 h-20 w-20 rounded-full bg-primary-200/40 dark:bg-navy-600/40 animate-ping"></div>
        <!-- Anel principal girando -->
        <div class="relative h-20 w-20 rounded-full border-4 border-primary-200 dark:border-navy-600 border-t-primary-600 dark:border-t-primary-500 animate-spin shadow-lg"></div>
        <!-- Ícone central animado -->
        <div class="absolute inset-0 flex items-center justify-center">
            <svg class="w-9 h-9 text-primary-600 dark:text-primary-400 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2l1 3h12l1-3h2M5 13l4-8h6l4 8M10 9h4" />
            </svg>
        </div>
    </div>
    <!-- Texto e barra de progresso -->
    <div class="mt-8 text-center space-y-3">
        <p class="text-sm font-semibold text-primary-700 dark:text-primary-300 tracking-wide animate-pulse">Carregando...</p>
        <div class="w-48 h-1 bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-primary-500 via-primary-600 to-primary-500 dark:from-primary-400 dark:via-primary-500 dark:to-primary-400 animate-[loading_1.5s_ease-in-out_infinite] rounded-full"></div>
        </div>
    </div>
</div>

<style>
    @keyframes loading {
        0%, 100% { transform: translateX(-100%); }
        50% { transform: translateX(100%); }
    }
</style>

<!-- Sidebar Desktop & Mobile Offcanvas -->
@include('layouts.sidebar')

<!-- Main Wrapper ajusta margem conforme sidebar -->
<div class="layout-shell flex flex-col">
    <!-- Top Bar -->
    <header class="sticky top-0 z-30 h-16 flex items-center gap-3 px-4 lg:px-6 bg-white/85 dark:bg-navy-800/90 backdrop-blur border-b border-gray-200 dark:border-navy-700 shadow-sm">
        <!-- User Dropdown (mobile esquerda, desktop direita) -->
        <div class="relative order-1 lg:order-5" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="inline-flex items-center gap-2 pl-3 pr-3 h-10 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition">
                <span class="hidden sm:inline-block max-w-[8rem] truncate">{{ Auth::user()->name }}</span>
                <x-icon name="chevron-down" class="h-4 w-4 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" x-cloak x-transition.origin-top-left class="absolute left-0 lg:left-auto lg:right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-700 py-1 z-20 overflow-hidden">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-navy-50 hover:bg-primary-50 dark:hover:bg-navy-700/60">Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-navy-50 hover:bg-primary-50 dark:hover:bg-navy-700/60">Sair</button>
                </form>
            </div>
        </div>

        <!-- Título -->
        <div class="flex-1 flex items-center min-w-0 order-2 lg:order-2">
            @isset($header)
                <div class="text-gray-700 dark:text-navy-50 font-semibold truncate">{{ $header }}</div>
            @endisset
        </div>

        @isset($pageActions)
            <div class="order-3 flex items-center gap-2">{{ $pageActions }}</div>
        @endisset

        <!-- Toggle Dark Mode -->
        <button @click="toggleDark" class="order-4 inline-flex items-center justify-center h-10 w-10 rounded-md text-primary-600 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition" x-bind:title="darkMode ? 'Modo claro' : 'Modo escuro'">
            <x-icon name="sun" x-show="!darkMode" class="h-5 w-5" />
            <x-icon name="moon" x-show="darkMode" x-cloak class="h-5 w-5" />
        </button>

        <!-- Hamburger agora à direita somente mobile -->
        <button @click="openMobile" aria-controls="mobile-sidebar" x-bind:aria-expanded="isMobileSidebarOpen" class="order-5 lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-md text-primary-600 dark:text-navy-100 hover:bg-primary-50 dark:hover:bg-navy-700/60 transition" aria-label="Abrir menu">
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

{{-- Ensure page-level scripts pushed with @push('scripts') are injected --}}
@stack('scripts')

@unless(request()->routeIs('chat.*'))
    <a href="{{ route('chat.index') }}"
       title="Abrir Chat"
       aria-label="Abrir Chat"
       class="fixed bottom-6 right-6 z-50 p-3 bg-primary-600 text-white rounded-full shadow-lg
                  hover:bg-primary-700 transition-all duration-300 transform hover:scale-105">

        <x-icon name="chat-bubble-left-right" />
    </a>
@endunless
</body>
</html>
