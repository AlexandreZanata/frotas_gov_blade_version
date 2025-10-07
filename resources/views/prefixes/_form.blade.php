@csrf
<div class="grid gap-4 md:grid-cols-1">
    <div>
        <x-input-label for="name" value="Nome do Prefixo" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $prefix->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>
</div>
<div class="flex items-center gap-3 pt-6">
    <x-primary-button icon="save" compact>Salvar</x-primary-button>
    <a href="{{ route('prefixes.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
</div>
