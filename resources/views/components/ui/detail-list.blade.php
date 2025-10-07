@props(['items'=>[]])
<dl class="divide-y divide-gray-200 dark:divide-navy-700 text-sm">
    @foreach($items as $row)
        @php($isEmph = $row['bold'] ?? false)
        <div class="py-2 grid grid-cols-3 gap-2">
            <dt class="text-gray-500 dark:text-navy-200 whitespace-nowrap">{{ $row['label'] }}</dt>
            <dd class="col-span-2 text-gray-700 dark:text-navy-100 {{ $isEmph ? 'font-medium' : '' }}">{!! $row['value'] !!}</dd>
        </div>
    @endforeach
</dl>
