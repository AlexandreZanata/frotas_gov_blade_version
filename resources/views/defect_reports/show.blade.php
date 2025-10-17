<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Detalhes do Relatório de Defeito" icon="exclamation-triangle" />
    </x-slot>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <x-ui.card title="Itens Reportados">
                <div class="space-y-4">
                    @foreach($defectReport->answers as $answer)
                        <div class="p-4 border rounded-lg">
                            <p class="font-semibold">{{ $answer->item->name }}</p>
                            <p class="text-sm text-gray-500">{{ $answer->item->category->name }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="font-medium">Gravidade:</span>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($answer->severity == 'high') bg-red-200 text-red-800
                                    @elseif($answer->severity == 'medium') bg-yellow-200 text-yellow-800
                                    @else bg-gray-200 text-gray-800 @endif">
                                    {{ ucfirst($answer->severity) }}
                                </span>
                            </div>
                            @if($answer->notes)
                                <p class="text-sm mt-2 bg-gray-50 p-2 rounded"><strong>Obs:</strong> {{ $answer->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Informações Gerais">
                @php($items=[
                   ['label'=>'Veículo','value'=> $defectReport->vehicle->prefix->name . ' - ' . $defectReport->vehicle->name],
                   ['label'=>'Placa','value'=> $defectReport->vehicle->plate],
                   ['label'=>'Secretaria','value'=> $defectReport->vehicle->secretariat->name],
                   ['label'=>'Reportado por','value'=> $defectReport->user->name],
                   ['label'=>'Data','value'=> $defectReport->created_at->format('d/m/Y H:i')],
                   ['label'=>'Status','value'=> ucfirst($defectReport->status)],
               ])
                <x-ui.detail-list :items="$items" />
                @if($defectReport->notes)
                    <div class="mt-4">
                        <h4 class="font-semibold text-sm mb-1">Observações Gerais:</h4>
                        <p class="text-sm bg-gray-50 p-3 rounded-md">{{ $defectReport->notes }}</p>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
