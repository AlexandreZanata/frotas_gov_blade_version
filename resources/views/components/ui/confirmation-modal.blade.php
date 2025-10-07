{{-- resources/views/components/ui/confirmation-modal.blade.php --}}
@props([
    'title' => 'Confirmar Ação',
    'icon' => 'alert-triangle',
    'iconClass' => 'text-amber-600 dark:text-amber-400',
    'maxWidth' => 'md'
])

@php
    $maxWidthClasses = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth] ?? 'sm:max-w-md';
@endphp

<div x-data="{ open: false }" @keydown.escape.window="open = false">
    {{-- 1. SLOT DO GATILHO --}}
    {{-- Coloque aqui qualquer botão, link ou ícone que irá abrir o modal --}}
    <div @click="open = true" class="inline-block cursor-pointer">
        {{ $trigger }}
    </div>

    {{-- Modal Backdrop --}}
    <div x-cloak x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
         aria-labelledby="confirmation-title"
         role="dialog"
         aria-modal="true">

        {{-- Modal Panel --}}
        <div @click.away="open = false"
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full bg-white dark:bg-navy-800 rounded-lg shadow-2xl overflow-hidden mx-4 sm:mx-0 {{ $maxWidthClasses }}">

            {{-- Header --}}
            <div class="flex items-start justify-between px-6 py-4 border-b border-gray-200 dark:border-navy-700">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30">
                        <x-icon :name="$icon" class="w-6 h-6 {{ $iconClass }}" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-navy-50" id="confirmation-title">
                        {{ $title }}
                    </h3>
                </div>
                <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <x-icon name="x" class="w-5 h-5" />
                    <span class="sr-only">Fechar</span>
                </button>
            </div>

            {{-- 2. SLOT DO CONTEÚDO --}}
            {{-- Coloque aqui a mensagem de confirmação. Pode ser texto simples ou HTML complexo. --}}
            <div class="px-6 py-5 text-sm text-gray-700 dark:text-navy-200 leading-relaxed">
                {{ $slot }}
            </div>

            {{-- 3. SLOT DO RODAPÉ --}}
            {{-- Coloque aqui os botões de ação (Cancelar, Confirmar, etc.) --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-navy-900/50 border-t border-gray-200 dark:border-navy-700">
                {{ $footer }}
            </div>
        </div>
    </div>
</div>
