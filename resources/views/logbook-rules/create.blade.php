{{-- resources/views/logbook-rules/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Regra de Quilometragem" subtitle="Cadastro de regra" hide-title-mobile
                          icon="cog"/>
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('logbook-rules.index')" icon="arrow-left" title="Voltar" variant="neutral"/>
    </x-slot>

    <x-ui.card title="Informações da Regra">
        @php($rule = new \App\Models\logbook\LogbookRule())
        <form action="{{ route('logbook-rules.store') }}" method="POST" class="space-y-6">
            @include('logbook-rules._form')
        </form>
    </x-ui.card>
</x-app-layout>
