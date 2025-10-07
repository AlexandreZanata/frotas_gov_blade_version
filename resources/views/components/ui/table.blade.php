@props([
    'headers' => [],
    'zebra' => true,
    'searchable' => true,
    'searchPlaceholder' => 'Pesquisar...',
    'searchValue' => '',
    'pagination' => null,
])

@if($searchable)
    <div class="mb-4" x-data="{
        search: '{{ $searchValue }}',
        debounceTimer: null,
        submitSearch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.search);
                if (this.search === '') {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }, 800);
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
                @keydown.enter.prevent="submitSearch()"
                placeholder="{{ $searchPlaceholder }}"
                class="block w-full pl-10 pr-3 py-2 text-sm border border-gray-300 dark:border-navy-600 rounded-lg bg-white dark:bg-navy-700 text-gray-900 dark:text-navy-100 placeholder-gray-400 dark:placeholder-navy-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
            >
        </div>
    </div>
@endif

<div class="relative overflow-auto rounded-lg border border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800 shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="text-[11px] uppercase tracking-wide font-semibold text-gray-600 dark:text-navy-200 bg-gray-50 dark:bg-navy-700/60 border-b border-gray-200 dark:border-navy-600 sticky top-0 z-10">
        <tr>
            @foreach($headers as $h)
                <th scope="col" class="px-4 py-2 text-left whitespace-nowrap {{ $loop->last ? 'text-right' : '' }}">{{ $h }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody class="text-gray-700 dark:text-navy-50 divide-y divide-gray-100 dark:divide-navy-700">
            {{ $slot }}
        </tbody>
    </table>
</div>

@if($pagination && $pagination->hasPages())
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4 px-4 py-3 bg-white dark:bg-navy-800 border border-gray-200 dark:border-navy-700 rounded-lg">
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
                <a href="{{ $pagination->appends(request()->except('page'))->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                    Anterior
                </a>
            @endif

            <div class="hidden sm:flex items-center gap-1">
                @php
                    // Limitar a exibição de no máximo 10 páginas
                    $maxPages = 10;
                    $halfPages = floor($maxPages / 2);
                    $currentPage = $pagination->currentPage();
                    $lastPage = $pagination->lastPage();

                    // Calcular início e fim da paginação
                    if ($lastPage <= $maxPages) {
                        // Se tiver menos ou igual a 10 páginas, mostra todas
                        $start = 1;
                        $end = $lastPage;
                    } else {
                        // Se tiver mais de 10 páginas, centraliza em torno da página atual
                        $start = max(1, $currentPage - $halfPages);
                        $end = min($lastPage, $currentPage + $halfPages);

                        // Ajustar se estiver muito no início
                        if ($start == 1) {
                            $end = min($maxPages, $lastPage);
                        }
                        // Ajustar se estiver muito no fim
                        if ($end == $lastPage) {
                            $start = max(1, $lastPage - $maxPages + 1);
                        }
                    }
                @endphp

                @if($start > 1)
                    <a href="{{ $pagination->appends(request()->except('page'))->url(1) }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                        1
                    </a>
                    @if($start > 2)
                        <span class="px-2 text-gray-500">...</span>
                    @endif
                @endif

                @for($page = $start; $page <= $end; $page++)
                    @if($page == $pagination->currentPage())
                        <span class="px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $pagination->appends(request()->except('page'))->url($page) }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                @if($end < $pagination->lastPage())
                    @if($end < $pagination->lastPage() - 1)
                        <span class="px-2 text-gray-500">...</span>
                    @endif
                    <a href="{{ $pagination->appends(request()->except('page'))->url($pagination->lastPage()) }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
                        {{ $pagination->lastPage() }}
                    </a>
                @endif
            </div>

            @if($pagination->hasMorePages())
                <a href="{{ $pagination->appends(request()->except('page'))->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-700 dark:text-navy-200 bg-white dark:bg-navy-700 border border-gray-300 dark:border-navy-600 rounded hover:bg-gray-50 dark:hover:bg-navy-600 transition">
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
