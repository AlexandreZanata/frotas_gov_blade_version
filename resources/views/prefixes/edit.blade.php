<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Prefixo" subtitle="Atualizar identificador" hide-title-mobile icon="prefix" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('prefixes.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Dados do Prefixo">
        <form action="{{ route('prefixes.update', $prefix) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('prefixes._form')
        </form>
    </x-ui.card>
</x-app-layout>
