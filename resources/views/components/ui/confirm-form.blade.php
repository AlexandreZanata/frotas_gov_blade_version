@props([
    'action',
    'method' => 'POST',
    'message' => 'Confirmar ação?',
    'buttonClass' => 'inline-flex items-center justify-center h-7 w-7 rounded-md transition',
    'icon' => null,
    'variant' => 'danger',
    'iconOnly' => true,
    'title' => 'Confirmar ação',
    'requireBackup' => false,
    'requireConfirmationText' => false,
    'confirmationText' => 'Eu estou ciente',
])
@php
    $methodUpper = strtoupper($method);
    if(!$iconOnly) { $iconOnly = filter_var($attributes->get('icon-only') ?? false, FILTER_VALIDATE_BOOLEAN) || $iconOnly; }
    if($attributes->get('button-class')) { $buttonClass = $attributes->get('button-class'); }
    $btnClasses = [
        'danger' => 'text-red-600 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/30',
        'primary' => 'text-primary-600 hover:bg-primary-50 dark:text-navy-100 dark:hover:bg-navy-700/60',
        'neutral' => 'text-gray-600 hover:bg-gray-100 dark:text-navy-100 dark:hover:bg-navy-700/60',
    ][$variant] ?? '';
    $confirmMessage = strip_tags($message);
@endphp
<div x-data="{
    open: false,
    wantBackup: {{ $requireBackup ? 'true' : 'false' }},
    confirmText: '',
    get canSubmit() {
        @if($requireConfirmationText)
            return this.confirmText.toLowerCase() === '{{ strtolower($confirmationText) }}';
        @else
            return true;
        @endif
    }
}" {{ $attributes->except(['class','button-class']) }}>
    <button type="button" @click="open = true" class="{{ $buttonClass }} {{ $btnClasses }}">
        @if($icon)
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
        @if(trim($slot) && !$iconOnly)
            <span>{{ $slot }}</span>
        @elseif(trim($slot))
            <span class="sr-only">{{ $slot }}</span>
        @endif
    </button>

    <!-- Modal Backdrop -->
    <div x-cloak x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-out duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm px-4"
         @click="open = false">

        <!-- Modal -->
        <form method="POST" action="{{ $action }}" @click.stop
             x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-out duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md bg-white dark:bg-navy-800 rounded-lg shadow-2xl overflow-hidden mb-4 sm:mb-0">
            @csrf
            @if(!in_array($methodUpper,['GET','POST']))
                @method($methodUpper)
            @endif

            <!-- Header -->
            <div class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 dark:border-navy-700">
                <div class="flex-shrink-0 flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="flex-1 text-base sm:text-lg font-semibold text-gray-900 dark:text-navy-50 min-w-0 pr-2 break-words">{{ $title }}</h3>
                <button @click="open = false" type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-4 sm:px-6 py-4 max-h-[65vh] overflow-y-auto">
                <div class="space-y-4">
                    <p class="text-sm text-gray-700 dark:text-navy-200 leading-relaxed whitespace-pre-line">{{ $confirmMessage }}</p>

                    @if($requireBackup)
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" name="create_backup" value="1" x-model="wantBackup"
                                       class="mt-0.5 w-4 h-4 text-blue-600 bg-white dark:bg-navy-700 border-gray-300 dark:border-navy-600 rounded focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                <span class="text-sm text-gray-700 dark:text-navy-200 flex-1">
                                    <span class="font-semibold text-blue-700 dark:text-blue-400">Gerar backup em PDF</span>
                                    <span class="block text-xs text-gray-600 dark:text-navy-300 mt-1">Salvar todos os dados relacionados antes da exclusão (recomendado)</span>
                                </span>
                            </label>
                        </div>
                    @endif

                    @if($requireConfirmationText)
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-800 dark:text-navy-100">
                                Para confirmar a exclusão, digite exatamente:
                            </label>
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-md border border-red-200 dark:border-red-800">
                                <code class="text-sm font-bold text-red-700 dark:text-red-400">"{{ $confirmationText }}"</code>
                            </div>
                            <input type="text"
                                   x-model="confirmText"
                                   placeholder="Digite aqui..."
                                   class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 placeholder-gray-400 dark:placeholder-navy-400 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                   autocomplete="off">
                            <p class="text-xs text-gray-500 dark:text-navy-400 italic">
                                ⚠️ Esta ação é irreversível e todos os dados relacionados serão permanentemente excluídos.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-2 sm:gap-3 px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 dark:bg-navy-900/50">
                <button @click="open = false"
                        type="button"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded-md hover:bg-gray-50 dark:hover:bg-navy-600 focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                    Cancelar
                </button>
                <button type="submit"
                        :disabled="!canSubmit"
                        :class="!canSubmit ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-700 dark:hover:bg-red-700'"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-red-600 dark:bg-red-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 transition shadow-sm">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>
