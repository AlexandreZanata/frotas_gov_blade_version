@php($types = [
    'success' => 'bg-green-50 border-green-300 text-green-800 dark:bg-green-700/20 dark:text-green-200 dark:border-green-600',
    'error' => 'bg-red-50 border-red-300 text-red-800 dark:bg-red-700/30 dark:text-red-200 dark:border-red-600',
    'warning' => 'bg-yellow-50 border-yellow-300 text-yellow-800 dark:bg-yellow-700/30 dark:text-yellow-100 dark:border-yellow-600',
    'info' => 'bg-blue-50 border-blue-300 text-blue-800 dark:bg-blue-700/30 dark:text-blue-100 dark:border-blue-600'
])
@if(session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="rounded-md border px-4 py-3 text-sm font-medium flex items-start gap-3 {{ $types['success'] }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span class="flex-1">{{ session('success') }}</span>
        <button type="button" @click="show = false" class="opacity-70 hover:opacity-100">&times;</button>
    </div>
@endif
@if(session()->has('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="rounded-md border px-4 py-3 text-sm font-medium flex items-start gap-3 {{ $types['error'] }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        <span class="flex-1">{{ session('error') }}</span>
        <button type="button" @click="show = false" class="opacity-70 hover:opacity-100">&times;</button>
    </div>
@endif
@if(session()->has('warning'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="rounded-md border px-4 py-3 text-sm font-medium flex items-start gap-3 {{ $types['warning'] }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span class="flex-1">{{ session('warning') }}</span>
        <button type="button" @click="show = false" class="opacity-70 hover:opacity-100">&times;</button>
    </div>
@endif
@if(session()->has('info'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="rounded-md border px-4 py-3 text-sm font-medium flex items-start gap-3 {{ $types['info'] }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="flex-1">{{ session('info') }}</span>
        <button type="button" @click="show = false" class="opacity-70 hover:opacity-100">&times;</button>
    </div>
@endif
