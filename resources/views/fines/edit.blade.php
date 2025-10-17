<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Multa" subtitle="Editar multa de trânsito existente" hide-title-mobile icon="speed-camera" />
    </x-slot>

    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('fines.show', $fine)" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Editar Multa">
        <form action="{{ route('fines.update', $fine) }}" method="POST" enctype="multipart/form-data" id="fine-form">
            @csrf
            @method('PUT')

            <!-- Auto de Infração -->
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="clipboard" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    Auto de Infração
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="infraction_notice_number" value="Número do Auto (opcional)" />
                        <x-text-input
                            id="infraction_notice_number"
                            name="infraction_notice_number"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('infraction_notice_number', $fine->infractionNotice?->notice_number) }}"
                            placeholder="Digite ou deixe em branco para gerar automaticamente"
                        />
                    </div>
                    <div>
                        <x-input-label for="issuing_authority" value="Autoridade Emissora" />
                        <x-text-input
                            id="issuing_authority"
                            name="issuing_authority"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('issuing_authority', $fine->infractionNotice?->issuing_authority) }}"
                            placeholder="Ex: DETRAN-DF"
                        />
                    </div>
                </div>
            </div>

            <!-- Dados da Multa -->
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="car" class="w-5 h-5 text-green-600 dark:text-green-400" />
                    Dados da Multa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="vehicle_id" value="Veículo *" />
                        <x-ui.select name="vehicle_id" id="vehicle_id" class="mt-1" required>
                            <option value="">Selecione um veículo</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $fine->vehicle_id) == $vehicle->id)>
                                    {{ $vehicle->plate }} - {{ $vehicle->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="driver_id" value="Condutor *" />
                        <x-ui.select name="driver_id" id="driver_id" class="mt-1" required>
                            <option value="">Selecione um condutor</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" @selected(old('driver_id', $fine->driver_id) == $driver->id)>
                                    {{ $driver->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('driver_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="issued_at" value="Data da Infração *" />
                        <x-text-input
                            id="issued_at"
                            name="issued_at"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            value="{{ old('issued_at', $fine->issued_at->format('Y-m-d\TH:i')) }}"
                            required
                        />
                        <x-input-error :messages="$errors->get('issued_at')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="due_date" value="Data de Vencimento" />
                        <x-text-input
                            id="due_date"
                            name="due_date"
                            type="date"
                            class="mt-1 block w-full"
                            value="{{ old('due_date', $fine->due_date?->format('Y-m-d')) }}"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="location" value="Local da Infração" />
                        <x-text-input
                            id="location"
                            name="location"
                            type="text"
                            class="mt-1 block w-full"
                            value="{{ old('location', $fine->location) }}"
                            placeholder="Ex: Av. Paulista, 1000 - São Paulo/SP"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="description" value="Descrição da Multa *" />
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Descreva os detalhes da multa..."
                            required
                        >{{ old('description', $fine->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Status da Multa -->
            <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <x-icon name="status" class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    Status da Multa
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="status" value="Status *" />
                        <x-ui.select name="status" id="status" class="mt-1" required>
                            <option value="pending_acknowledgement" @selected(old('status', $fine->status) == 'pending_acknowledgement')>Aguardando Ciência</option>
                            <option value="pending_payment" @selected(old('status', $fine->status) == 'pending_payment')>Aguardando Pagamento</option>
                            <option value="paid" @selected(old('status', $fine->status) == 'paid')>Pago</option>
                            <option value="appealed" @selected(old('status', $fine->status) == 'appealed')>Recorrida</option>
                            <option value="cancelled" @selected(old('status', $fine->status) == 'cancelled')>Cancelada</option>
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('fines.show', $fine) }}" class="px-4 py-2 text-sm text-gray-600 dark:text-navy-200 hover:underline">
                    Cancelar
                </a>
                <x-primary-button icon="save" compact>
                    Atualizar Multa
                </x-primary-button>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
