{{-- resources/views/logbook-rules/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Regra de Quilometragem" subtitle="Atualização de dados" hide-title-mobile icon="cog" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook-rules.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações da Regra">
        <form action="{{ route('logbook-rules.update', $logbookRule) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('logbook-rules._form')
        </form>
    </x-ui.card>
</x-app-layout>
