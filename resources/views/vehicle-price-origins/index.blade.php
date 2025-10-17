<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Patrimônios de Veículos" subtitle="Gestão dos valores de aquisição" hide-title-mobile icon="currency-dollar" />
    </x-slot>
    <x-slot name="pageActions">
        <a href="{{ route('vehicle-price-origins.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Novo</span>
        </a>
    </x-slot>

    <x-ui.card>
        <x-ui.table
            :headers="['Veículo','Placa','Valor','Data Aquisição','Tipo Aquisição','Ações']"
            :searchable="true"
            search-placeholder="Pesquisar por veículo ou placa..."
            :search-value="$search ?? ''"
            :pagination="$priceOrigins">
            @forelse($priceOrigins as $priceOrigin)
                <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                    <td class="px-4 py-2 font-medium">{{ $priceOrigin->vehicle->name }}</td>
                    <td class="px-4 py-2 uppercase tracking-wide">{{ $priceOrigin->vehicle->plate }}</td>
                    <td class="px-4 py-2 font-semibold text-green-600 dark:text-green-400">{{ $priceOrigin->formatted_amount }}</td>
                    <td class="px-4 py-2">{{ $priceOrigin->formatted_acquisition_date }}</td>
                    <td class="px-4 py-2">{{ $priceOrigin->acquisitionType->name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <x-ui.action-icon :href="route('vehicle-price-origins.show', $priceOrigin)" icon="eye" title="Ver" variant="primary" />
                            <x-ui.action-icon :href="route('vehicle-price-origins.edit', $priceOrigin)" icon="edit" title="Editar" variant="info" />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum patrimônio cadastrado.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-app-layout>
