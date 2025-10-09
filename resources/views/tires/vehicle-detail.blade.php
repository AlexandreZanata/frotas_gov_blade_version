<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-car mr-2"></i> {{ $vehicle->name }} - Gestão de Pneus
            </h2>
            <x-ui.secondary-button :href="route('tires.vehicles')">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </x-ui.secondary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Informações do Veículo --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Placa</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $vehicle->plate }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Categoria</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $vehicle->category->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total de Pneus</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $vehicle->tires->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- Diagrama Interativo de Pneus --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-diagram-project mr-2"></i> Diagrama de Pneus - {{ $layout->name }}
                </h3>

                <div class="relative bg-gray-100 dark:bg-gray-700 rounded-lg p-8" style="min-height: 500px;">
                    {{-- SVG do veículo (simplificado) --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-20">
                        <svg width="60%" height="80%" viewBox="0 0 100 100" class="text-gray-400">
                            <rect x="30" y="20" width="40" height="60" rx="5" fill="currentColor" />
                        </svg>
                    </div>

                    {{-- Posições dos Pneus --}}
                    @foreach($layout->layout_data['positions'] as $position)
                        @php
                            $tire = $vehicle->tires->where('current_position', $position['id'])->first();
                            $conditionColors = [
                                'Novo' => 'bg-green-500 border-green-600',
                                'Bom' => 'bg-blue-500 border-blue-600',
                                'Atenção' => 'bg-yellow-500 border-yellow-600',
                                'Crítico' => 'bg-red-500 border-red-600',
                            ];
                            $color = $tire ? ($conditionColors[$tire->condition] ?? 'bg-gray-400 border-gray-500') : 'bg-gray-300 border-gray-400';
                        @endphp

                        <div class="absolute tire-position cursor-pointer hover:scale-110 transition-transform"
                             style="left: {{ $position['x'] }}%; top: {{ $position['y'] }}%; transform: translate(-50%, -50%);"
                             onclick="openTireModal({{ $position['id'] }}, '{{ $position['name'] }}', {{ $tire ? $tire->id : 'null' }})"
                             data-position="{{ $position['id'] }}"
                             data-tire-id="{{ $tire?->id }}">

                            {{-- Círculo do Pneu --}}
                            <div class="relative w-20 h-20 rounded-full {{ $color }} border-4 shadow-lg flex items-center justify-center group">
                                {{-- Label da Posição --}}
                                <div class="text-white font-bold text-xs text-center">
                                    <div>{{ $position['label'] }}</div>
                                    @if($tire)
                                        <div class="text-xs mt-1">{{ number_format(($tire->current_km / $tire->lifespan_km) * 100, 0) }}%</div>
                                    @endif
                                </div>

                                {{-- Ícone de Alerta --}}
                                @if($tire && $tire->condition === 'Crítico')
                                    <div class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                        <i class="fas fa-exclamation text-xs"></i>
                                    </div>
                                @elseif($tire && $tire->condition === 'Atenção')
                                    <div class="absolute -top-2 -right-2 bg-yellow-500 text-white rounded-full w-6 h-6 flex items-center justify-center">
                                        <i class="fas fa-exclamation text-xs"></i>
                                    </div>
                                @endif

                                {{-- Tooltip --}}
                                <div class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                    @if($tire)
                                        {{ $tire->brand }} {{ $tire->model }}<br>
                                        {{ $tire->serial_number }}
                                    @else
                                        Sem pneu
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Legenda --}}
                <div class="mt-6 flex flex-wrap gap-4 justify-center">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-green-500"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Novo</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Bom</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-yellow-500"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Atenção</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-red-500"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Crítico</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-gray-300"></div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Vazio</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal de Ações do Pneu --}}
    <div id="tireModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="modalTitle">
                    Ações do Pneu
                </h3>
                <button onclick="closeTireModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="modalContent" class="space-y-4">
                {{-- Conteúdo será preenchido dinamicamente --}}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentPosition = null;
        let currentTireId = null;
        let vehicleId = '{{ $vehicle->id }}';

        function openTireModal(position, positionName, tireId) {
            currentPosition = position;
            currentTireId = tireId;

            document.getElementById('modalTitle').textContent = positionName;

            if (tireId) {
                showTireActions(tireId);
            } else {
                showInstallOptions();
            }

            document.getElementById('tireModal').classList.remove('hidden');
        }

        function closeTireModal() {
            document.getElementById('tireModal').classList.add('hidden');
        }

        function showTireActions(tireId) {
            // Buscar informações do pneu
            fetch(`/api/tires/${tireId}`)
                .then(response => response.json())
                .then(tire => {
                    const content = `
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Informações do Pneu</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="text-gray-600 dark:text-gray-400">Marca/Modelo:</span> ${tire.brand} ${tire.model}</div>
                                <div><span class="text-gray-600 dark:text-gray-400">Série:</span> ${tire.serial_number}</div>
                                <div><span class="text-gray-600 dark:text-gray-400">Condição:</span> <span class="badge-${tire.condition.toLowerCase()}">${tire.condition}</span></div>
                                <div><span class="text-gray-600 dark:text-gray-400">Uso:</span> ${tire.current_km.toLocaleString()} / ${tire.lifespan_km.toLocaleString()} km</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <button onclick="showRotateForm()" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-sync mr-2"></i> Fazer Rodízio
                            </button>
                            <button onclick="showReplaceForm()" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-exchange-alt mr-2"></i> Trocar Pneu
                            </button>
                            <button onclick="showRemoveForm()" class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                <i class="fas fa-arrow-right mr-2"></i> Remover (Estoque/Manutenção)
                            </button>
                            <button onclick="showEventForm()" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-history mr-2"></i> Registrar Evento
                            </button>
                            <a href="/tires/history/${tireId}" class="block w-full px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-center">
                                <i class="fas fa-list mr-2"></i> Ver Histórico
                            </a>
                        </div>
                    `;
                    document.getElementById('modalContent').innerHTML = content;
                });
        }

        function showInstallOptions() {
            const content = `
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 rounded-lg mb-4">
                    <p class="text-yellow-800 dark:text-yellow-200">
                        <i class="fas fa-info-circle mr-2"></i>
                        Esta posição está vazia. Selecione um pneu do estoque para instalar.
                    </p>
                </div>
                <button onclick="showReplaceForm()" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i> Instalar Pneu do Estoque
                </button>
            `;
            document.getElementById('modalContent').innerHTML = content;
        }

        function showRotateForm() {
            const content = `
                <form onsubmit="submitRotate(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Selecione o pneu para trocar de posição:
                        </label>
                        <select name="tire_2_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Selecione...</option>
                            @foreach($vehicle->tires as $t)
                                <option value="{{ $t->id }}" data-position="{{ $t->current_position }}">
                                    Posição {{ $t->current_position }} - {{ $t->brand }} {{ $t->model }} ({{ $t->serial_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            KM do Veículo:
                        </label>
                        <input type="number" name="km_at_event" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Executar Rodízio
                        </button>
                        <button type="button" onclick="showTireActions(currentTireId)" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Voltar
                        </button>
                    </div>
                </form>
            `;
            document.getElementById('modalContent').innerHTML = content;
        }

        function showReplaceForm() {
            const content = `
                <form onsubmit="submitReplace(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Selecione o novo pneu do estoque:
                        </label>
                        <select name="new_tire_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Selecione...</option>
                            @foreach($availableTires as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->brand }} {{ $t->model }} - {{ $t->serial_number }} ({{ $t->condition }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo da Troca:
                        </label>
                        <textarea name="reason" required rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            KM do Veículo:
                        </label>
                        <input type="number" name="km_at_event" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Executar Troca
                        </button>
                        <button type="button" onclick="${currentTireId ? 'showTireActions(currentTireId)' : 'showInstallOptions()'}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Voltar
                        </button>
                    </div>
                </form>
            `;
            document.getElementById('modalContent').innerHTML = content;
        }

        function showRemoveForm() {
            const content = `
                <form onsubmit="submitRemove(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Novo Status:
                        </label>
                        <select name="new_status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100">
                            <option value="Em Estoque">Em Estoque</option>
                            <option value="Em Manutenção">Em Manutenção</option>
                            <option value="Recapagem">Recapagem</option>
                            <option value="Descartado">Descartado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo:
                        </label>
                        <textarea name="reason" required rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            KM do Veículo:
                        </label>
                        <input type="number" name="km_at_event" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            Remover Pneu
                        </button>
                        <button type="button" onclick="showTireActions(currentTireId)" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Voltar
                        </button>
                    </div>
                </form>
            `;
            document.getElementById('modalContent').innerHTML = content;
        }

        function showEventForm() {
            const content = `
                <form onsubmit="submitEvent(event)" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo de Evento:
                        </label>
                        <select name="event_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100">
                            <option value="Manutenção">Manutenção</option>
                            <option value="Recapagem">Recapagem</option>
                            <option value="Descarte">Descarte</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrição:
                        </label>
                        <textarea name="description" required rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-100"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Registrar Evento
                        </button>
                        <button type="button" onclick="showTireActions(currentTireId)" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Voltar
                        </button>
                    </div>
                </form>
            `;
            document.getElementById('modalContent').innerHTML = content;
        }

        async function submitRotate(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const tire2Select = event.target.querySelector('[name="tire_2_id"]');
            const position2 = tire2Select.options[tire2Select.selectedIndex].dataset.position;

            try {
                const response = await fetch('/tires/rotate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        vehicle_id: vehicleId,
                        tire_1_id: currentTireId,
                        tire_2_id: formData.get('tire_2_id'),
                        position_1: currentPosition,
                        position_2: parseInt(position2),
                        km_at_event: parseInt(formData.get('km_at_event'))
                    })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Rodízio realizado com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao executar rodízio: ' + error.message);
            }
        }

        async function submitReplace(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            try {
                const response = await fetch('/tires/replace', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        vehicle_id: vehicleId,
                        old_tire_id: currentTireId,
                        new_tire_id: formData.get('new_tire_id'),
                        position: currentPosition,
                        km_at_event: parseInt(formData.get('km_at_event')),
                        reason: formData.get('reason')
                    })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Troca realizada com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao executar troca: ' + error.message);
            }
        }

        async function submitRemove(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            try {
                const response = await fetch('/tires/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        tire_id: currentTireId,
                        new_status: formData.get('new_status'),
                        km_at_event: parseInt(formData.get('km_at_event')),
                        reason: formData.get('reason')
                    })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Pneu removido com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao remover pneu: ' + error.message);
            }
        }

        async function submitEvent(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            try {
                const response = await fetch('/tires/register-event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        tire_id: currentTireId,
                        event_type: formData.get('event_type'),
                        description: formData.get('description')
                    })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Evento registrado com sucesso!');
                    closeTireModal();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                alert('Erro ao registrar evento: ' + error.message);
            }
        }

        // Fechar modal ao clicar fora
        document.getElementById('tireModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTireModal();
            }
        });
    </script>
    @endpush
</x-app-layout>

