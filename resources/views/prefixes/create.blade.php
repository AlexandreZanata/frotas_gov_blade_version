<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Prefixo" subtitle="Cadastrar identificador">
            <x-slot name="actions">
                <a href="{{ route('prefixes.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Voltar</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card title="Dados do Prefixo">
        @php($prefix = new \App\Models\Prefix())
        <form action="{{ route('prefixes.store') }}" method="POST" class="space-y-6">
            @include('prefixes._form')
        </form>
    </x-ui.card>
</x-app-layout>
