@props(['message' => 'Tem certeza que deseja realizar esta ação?', 'title' => 'Confirmar ação', 'confirmText' => 'Confirmar', 'cancelText' => 'Cancelar'])
<div x-data="{ open: false }" @keydown.escape.window="open = false">
    <div @click="open = true">
        {{ $trigger }}
    </div>

    <!-- Modal Backdrop -->
    <div x-cloak x-show="open"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm px-4"
         @click="open = false">

        <!-- Modal -->
        <div @click.stop
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md bg-white dark:bg-navy-800 rounded-lg shadow-2xl overflow-hidden mb-4 sm:mb-0">

            <!-- Header -->
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200 dark:border-navy-700">
                <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="flex-1 text-lg font-semibold text-gray-900 dark:text-navy-50">{{ $title }}</h3>
                <button @click="open = false" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-navy-200 leading-relaxed">{{ $message }}</p>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-navy-900/50">
                <button @click="open = false"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-md hover:bg-gray-50 dark:hover:bg-navy-600 focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                    {{ $cancelText }}
                </button>
                <button @click="open = false; $dispatch('confirmed')"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 transition shadow-sm">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

