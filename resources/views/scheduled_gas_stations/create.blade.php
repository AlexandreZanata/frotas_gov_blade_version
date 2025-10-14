<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Agendamento de Posto" subtitle="Agendar um posto para ficar ativo para abastecimentos" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('scheduled_gas_stations.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Detalhes do Agendamento">
        @php($scheduledGasStation = new \App\Models\ScheduledGasStation())
        <form action="{{ route('scheduled_gas_stations.store') }}" method="POST" class="space-y-6">
            @include('scheduled_gas_stations._form')
        </form>
    </x-ui.card>
</x-app-layout>
