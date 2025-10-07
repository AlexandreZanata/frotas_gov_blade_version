@props([
    'headers' => [],
    'searchable' => true,
    'searchPlaceholder' => 'Pesquisar...',
    'searchValue' => '',
    'searchRoute' => null,
    'pagination' => null,
])

<div class="space-y-4">
    @if($searchable)
        <div class="flex items-center gap-3">
            <div class="flex-1 relative" x-data="{
                search: '{{ $searchValue }}',
                debounceTimer: null,
                submitSearch() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        if ('{{ $searchRoute }}') {
                            const url = new URL('{{ $searchRoute }}', window.location.origin);
                            url.searchParams.set('search', this.search);
                            window.location.href = url.toString();
                        } else {
                            const form = this.$el.closest('form') || this.$el.querySelector('form');
                            if (form) form.submit();
                        }
                    }, 500);
                }
            }">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400 dark:text-navy-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        x-model="search"
                        @input="submitSearch()"
                        placeholder="{{ $searchPlaceholder }}"
                        class="block w-full pl-10 pr-3 py-2 text-sm border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 placeholder-gray-400 dark:placeholder-navy-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                    >
                </div>
            </div>
        </div>
    @endif

    <div class="relative overflow-auto rounded-lg border border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800 shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="text-[11px] uppercase tracking-wide font-semibold text-gray-600 dark:text-navy-200 bg-gray-50 dark:bg-navy-700/60 border-b border-gray-200 dark:border-navy-600 sticky top-0 z-10">
            <tr>
                @foreach($headers as $h)
                    <th scope="col" class="px-4 py-2 text-left whitespace-nowrap">{{ $h }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody class="text-gray-700 dark:text-navy-50 divide-y divide-gray-100 dark:divide-navy-700">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if($pagination && $pagination->hasPages())
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-4 py-3 bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-700 rounded-lg">
            <div class="text-sm text-gray-700 dark:text-navy-300">
                Mostrando <span class="font-medium">{{ $pagination->firstItem() ?? 0 }}</span>
                a <span class="font-medium">{{ $pagination->lastItem() ?? 0 }}</span>
                de <span class="font-medium">{{ $pagination->total() }}</span> resultados
            </div>

            <div class="flex items-center gap-2">
                @if($pagination->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-400 dark:text-navy-500 bg-gray-100 dark:bg-navy-700 rounded cursor-not-allowed">
                        Anterior
                    </span>
                @else
                    <a href="{{ $pagination->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                        Anterior
                    </a>
                @endif

                <div class="hidden sm:flex items-center gap-1">
                    @foreach($pagination->getUrlRange(max(1, $pagination->currentPage() - 2), min($pagination->lastPage(), $pagination->currentPage() + 2)) as $page => $url)
                        @if($page == $pagination->currentPage())
                            <span class="px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                </div>

                @if($pagination->hasMorePages())
                    <a href="{{ $pagination->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                        Próximo
                    </a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-400 dark:text-navy-500 bg-gray-100 dark:bg-navy-700 rounded cursor-not-allowed">
                        Próximo
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

