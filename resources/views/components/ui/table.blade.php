@props(['headers'=>[]])
<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-navy-700 bg-white dark:bg-navy-800">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700 text-sm">
        <thead class="bg-gray-50 dark:bg-navy-700/40 text-gray-700 dark:text-navy-100">
        <tr>
            @foreach($headers as $h)
                <th scope="col" class="font-semibold px-4 py-2 text-left uppercase tracking-wide text-xs">{{ $h }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-navy-700 text-gray-700 dark:text-navy-50">
        {{ $slot }}
        </tbody>
    </table>
</div>

