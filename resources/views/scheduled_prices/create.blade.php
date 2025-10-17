<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Agendamento de Preço"
                          subtitle="Agendar um novo valor para um tipo de combustível"/>
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('scheduled_prices.index')" icon="arrow-left" title="Voltar" variant="neutral"/>
    </x-slot>

    <x-ui.card title="Detalhes do Agendamento">
        @php($scheduledPrice = new \App\Models\fuel\ScheduledPrice())
        <form action="{{ route('scheduled_prices.store') }}" method="POST" class="space-y-6">
            @include('scheduled_prices._form')
        </form>
    </x-ui.card>
</x-app-layout>
