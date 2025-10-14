@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="gas_station_id" value="Posto de Gasolina" />
        <x-ui.select name="gas_station_id" id="gas_station_id" class="mt-1" required>
            <option value="">Selecione um posto...</option>
            @foreach($gasStations as $station)
                <option value="{{ $station->id }}" @selected(old('gas_station_id', $scheduledPrice->gas_station_id ?? '') == $station->id)>
                    {{ $station->name }}
                </option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('gas_station_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="fuel_type_id" value="Tipo de Combustível" />
        <x-ui.select name="fuel_type_id" id="fuel_type_id" class="mt-1" required>
            <option value="">Selecione um combustível...</option>
            @foreach($fuelTypes as $fuel)
                <option value="{{ $fuel->id }}" @selected(old('fuel_type_id', $scheduledPrice->fuel_type_id ?? '') == $fuel->id)>
                    {{ $fuel->name }}
                </option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="price" value="Preço (ex: 5.499)" />
        <x-text-input id="price" name="price" type="number" step="0.001" class="mt-1 block w-full" :value="old('price', $scheduledPrice->price ?? '')" required />
        <x-input-error :messages="$errors->get('price')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="start_date" value="Data de Início da Vigência" />
        <x-text-input id="start_date" name="start_date" type="datetime-local" class="mt-1 block w-full" :value="old('start_date', $scheduledPrice->start_date ? $scheduledPrice->start_date->format('Y-m-d\TH:i') : '')" required />
        <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="end_date" value="Data de Fim (Opcional)" />
        <x-text-input id="end_date" name="end_date" type="datetime-local" class="mt-1 block w-full" :value="old('end_date', $scheduledPrice->end_date ? $scheduledPrice->end_date->format('Y-m-d\TH:i') : '')" />
        <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
    </div>
</div>

<div class="flex items-center gap-3 pt-6">
    <x-primary-button icon="save" compact>Salvar Agendamento</x-primary-button>
    <a href="{{ route('scheduled_prices.index') }}" class="text-sm text-gray-600 dark:text-gray-200 hover:underline">Cancelar</a>
</div>
