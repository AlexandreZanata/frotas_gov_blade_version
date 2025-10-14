<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Preços Atuais" subtitle="Tabela de preços de combustíveis vigentes" />
    </x-slot>

    <x-ui.card>
        {{-- 1. Adicione um div com uma classe única ao redor da tabela --}}
        <div class="fuel-prices-table">
            <x-ui.table
                {{-- 2. O header permanece como uma string simples --}}
                :headers="['Posto', 'Combustível', 'Preço', 'Em Vigor Desde']"
                :pagination="$fuelPrices">

                @forelse($fuelPrices as $price)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                        <td class="px-4 py-2 font-medium">{{ $price->gasStation->name }}</td>
                        <td class="px-4 py-2">{{ $price->fuelType->name }}</td>
                        <td class="px-4 py-2 font-mono font-semibold">R$ {{ number_format($price->price, 3, ',', '.') }}</td>

                        {{-- 3. Adicione a classe text-right na última célula --}}
                        <td class="px-4 py-2 text-right">{{ $price->effective_date->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">Nenhum preço vigente encontrado.</td>
                    </tr>
                @endforelse
            </x-ui.table>
        </div>
    </x-ui.card>
</x-app-layout>

{{-- 4. Adicione este bloco de estilo no final do arquivo --}}
@push('styles')
    <style>
        .fuel-prices-table th:last-child {
            text-align: right;
        }
    </style>
@endpush
