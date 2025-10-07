@props(['name','id'=>null,'placeholder'=>null])
<select name="{{ $name }}" id="{{ $id ?? $name }}" {{ $attributes->merge(['class'=>'block w-full rounded-md border-gray-300 dark:border-navy-600 dark:bg-navy-700 dark:text-navy-50 focus:border-primary-500 focus:ring-primary-500 text-sm']) }}>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>

