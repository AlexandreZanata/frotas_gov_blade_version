@props(['item', 'name', 'previousStatus' => null, 'previousNotes' => null])

<div
    x-data="{
        status: '{{ $previousStatus ?? '' }}',
        notes: '{{ $previousNotes ?? '' }}',
        showNotes: {{ $previousStatus === 'problem' ? 'true' : 'false' }}
    }"
    class="rounded-lg border border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800 p-4"
>
    <div class="space-y-3">
        <!-- Item Header -->
        <div>
            <h4 class="font-medium text-gray-900 dark:text-navy-50">{{ $item->name }}</h4>
            @if($item->description)
                <p class="text-sm text-gray-500 dark:text-navy-300 mt-1">{{ $item->description }}</p>
            @endif
        </div>

        <!-- Status Buttons -->
        <div class="flex gap-2">
            <button
                type="button"
                @click="status = 'ok'; showNotes = false"
                :class="status === 'ok' ? 'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/30' : 'bg-gray-50 dark:bg-navy-700'"
                class="flex-1 rounded-lg p-3 transition-all hover:shadow-md"
            >
                <div class="flex flex-col items-center gap-1">
                    <div class="rounded-full p-2" :class="status === 'ok' ? 'bg-green-500' : 'bg-gray-300 dark:bg-navy-600'">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium" :class="status === 'ok' ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-navy-300'">OK</span>
                </div>
            </button>

            <button
                type="button"
                @click="status = 'attention'; showNotes = false"
                :class="status === 'attention' ? 'ring-2 ring-yellow-500 bg-yellow-50 dark:bg-yellow-900/30' : 'bg-gray-50 dark:bg-navy-700'"
                class="flex-1 rounded-lg p-3 transition-all hover:shadow-md"
            >
                <div class="flex flex-col items-center gap-1">
                    <div class="rounded-full p-2" :class="status === 'attention' ? 'bg-yellow-500' : 'bg-gray-300 dark:bg-navy-600'">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium" :class="status === 'attention' ? 'text-yellow-700 dark:text-yellow-400' : 'text-gray-600 dark:text-navy-300'">Atenção</span>
                </div>
            </button>

            <button
                type="button"
                @click="status = 'problem'; showNotes = true"
                :class="status === 'problem' ? 'ring-2 ring-red-500 bg-red-50 dark:bg-red-900/30' : 'bg-gray-50 dark:bg-navy-700'"
                class="flex-1 rounded-lg p-3 transition-all hover:shadow-md"
            >
                <div class="flex flex-col items-center gap-1">
                    <div class="rounded-full p-2" :class="status === 'problem' ? 'bg-red-500' : 'bg-gray-300 dark:bg-navy-600'">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium" :class="status === 'problem' ? 'text-red-700 dark:text-red-400' : 'text-gray-600 dark:text-navy-300'">Problema</span>
                </div>
            </button>
        </div>

        <!-- Notes Field (shown when problem is selected) -->
        <div x-show="showNotes" x-transition class="space-y-2">
            <x-input-label :for="$name . '_notes'" value="Descreva o problema" />
            <textarea
                :name="'checklist[{{ $item->id }}][notes]'"
                :id="$name . '_notes'"
                x-model="notes"
                rows="3"
                class="block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-red-500 focus:ring-red-500"
                placeholder="Descreva detalhadamente o problema encontrado..."
                :required="status === 'problem'"
            ></textarea>
        </div>

        <!-- Hidden inputs -->
        <input type="hidden" :name="'checklist[{{ $item->id }}][status]'" x-model="status" required>
    </div>
</div>

