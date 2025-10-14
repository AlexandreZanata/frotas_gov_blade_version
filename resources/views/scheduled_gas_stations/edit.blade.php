<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Agendamento de Posto" subtitle="Atualizar dados do agendamento" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('scheduled_gas_stations.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Detalhes do Agendamento">
        <form action="{{ route('scheduled_gas_stations.update', $scheduledGasStation) }}" method="POST" class="space-y-6">
            @method('PUT')
            @include('scheduled_gas_stations._form')
        </form>
    </x-ui.card>
</x-app-layout>
