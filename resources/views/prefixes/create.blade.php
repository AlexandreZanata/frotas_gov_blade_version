<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Prefixo" subtitle="Cadastrar identificador" hide-title-mobile icon="prefix">
            <x-slot name="actions">
                <x-ui.action-icon :href="route('prefixes.index')" icon="arrow-left" title="Voltar" variant="neutral"/>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.card title="Dados do Prefixo">
        @php($prefix = new \App\Models\Vehicle\Prefix())
        <form action="{{ route('prefixes.store') }}" method="POST" class="space-y-6">
            @include('prefixes._form')
        </form>
    </x-ui.card>
</x-app-layout>
