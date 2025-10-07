@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="Nome" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $vehicle->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="brand" value="Marca" />
        <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full" :value="old('brand', $vehicle->brand ?? '')" required />
        <x-input-error :messages="$errors->get('brand')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="model_year" value="Ano / Modelo" />
        <x-text-input id="model_year" name="model_year" type="text" class="mt-1 block w-full" :value="old('model_year', $vehicle->model_year ?? '')" required />
        <x-input-error :messages="$errors->get('model_year')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="plate" value="Placa" />
        <x-text-input id="plate" name="plate" type="text" class="mt-1 block w-full uppercase" :value="old('plate', $vehicle->plate ?? '')" required />
        <x-input-error :messages="$errors->get('plate')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="fuel_tank_capacity" value="Capacidade Tanque (L)" />
        <x-text-input id="fuel_tank_capacity" name="fuel_tank_capacity" type="number" class="mt-1 block w-full" :value="old('fuel_tank_capacity', $vehicle->fuel_tank_capacity ?? '')" required />
        <x-input-error :messages="$errors->get('fuel_tank_capacity')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="fuel_type_id" value="Tipo de Combustível" />
        <x-ui.select name="fuel_type_id" id="fuel_type_id" class="mt-1" required>
            <option value="">Selecione...</option>
            @foreach(($fuelTypes ?? []) as $ft)
                <option value="{{ $ft->id }}" @selected(old('fuel_type_id', $vehicle->fuel_type_id ?? '') == $ft->id)>{{ $ft->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('fuel_type_id')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="category_id" value="Categoria" />
        <x-ui.select name="category_id" id="category_id" class="mt-1" required>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $vehicle->category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="prefix_id" value="Prefixo (opcional)" />
        <x-ui.select name="prefix_id" id="prefix_id" class="mt-1" placeholder="Selecione">
            <option value="">-- Nenhum --</option>
            @foreach($prefixes as $p)
                <option value="{{ $p->id }}" @selected(old('prefix_id', $vehicle->prefix_id ?? '') == $p->id)>{{ $p->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('prefix_id')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="status_id" value="Status" />
        <x-ui.select name="status_id" id="status_id" class="mt-1" required>
            @foreach($statuses as $s)
                <option value="{{ $s->id }}" @selected(old('status_id', $vehicle->status_id ?? '') == $s->id)>{{ $s->name ?? ('#'.$s->id) }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('status_id')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="chassis" value="Chassi" />
        <x-text-input id="chassis" name="chassis" type="text" class="mt-1 block w-full" :value="old('chassis', $vehicle->chassis ?? '')" />
        <x-input-error :messages="$errors->get('chassis')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="renavam" value="RENAVAM" />
        <x-text-input id="renavam" name="renavam" type="text" class="mt-1 block w-full" :value="old('renavam', $vehicle->renavam ?? '')" />
        <x-input-error :messages="$errors->get('renavam')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="registration" value="Registro" />
        <x-text-input id="registration" name="registration" type="text" class="mt-1 block w-full" :value="old('registration', $vehicle->registration ?? '')" />
        <x-input-error :messages="$errors->get('registration')" class="mt-1" />
    </div>
</div>
<div class="flex items-center gap-3 pt-6">
    <x-primary-button>Salvar</x-primary-button>
    <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
</div>
