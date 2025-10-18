<?php

use App\Http\Controllers\BackupReportController;
use App\Http\Controllers\DefectReportController;
use App\Http\Controllers\fuel\FuelPriceController;
use App\Http\Controllers\fuel\GasStationCurrentController;
use App\Http\Controllers\fuel\ScheduledGasStationController;
use App\Http\Controllers\fuel\ScheduledPriceController;
use App\Http\Controllers\runs\LogbookRuleController;
use App\Http\Controllers\users\DefaultPasswordController;
use App\Http\Controllers\users\ProfileController;
use App\Http\Controllers\users\UserController;
use App\Http\Controllers\vehicle\PrefixController;
use App\Http\Controllers\vehicle\VehicleCategoryController;
use App\Http\Controllers\vehicle\VehicleController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    $vehiclesInUse = collect();
    $stats = [
        'total' => 0,
        'bySecretariat' => collect()
    ];
    $chartData = [
        'series' => [],
        'categories' => []
    ];
    $expensesData = [
        'series' => [],
        'categories' => []
    ];
    $fuelingsData = [
        'series' => [],
        'categories' => []
    ];

    // Dados apenas para general_manager e sector_manager
    if ($user->hasAnyRole(['general_manager', 'sector_manager'])) {
        $query = \App\Models\Vehicle\Vehicle::query();

        // Filtra apenas veículos em uso (com diário de bordo em andamento)
        $query->whereHas('runs', function($q) {
            $q->whereNull('finished_at');
        });

        // Se for sector_manager, filtra pela secretaria
        if ($user->hasRole('sector_manager')) {
            $query->where('secretariat_id', $user->secretariat_id);
        }

        $vehiclesInUse = $query->with(['runs' => function($q) {
            $q->whereNull('finished_at')->latest()->limit(1);
        }, 'secretariat'])->limit(5)->get();

        // Estatísticas
        $totalInUse = $query->count();

        $bySecretariat = $query->clone()
            ->join('secretariats', 'vehicles.secretariat_id', '=', 'secretariats.id')
            ->selectRaw('secretariats.name as secretariat_name, count(*) as total')
            ->groupBy('secretariats.id', 'secretariats.name')
            ->get();

        $stats = [
            'total' => $totalInUse,
            'bySecretariat' => $bySecretariat
        ];

        // Dados para gráfico de veículos
        if ($bySecretariat->isNotEmpty()) {
            $chartData = [
                'series' => [
                    [
                        'name' => 'Veículos em Uso',
                        'data' => $bySecretariat->pluck('total')->toArray()
                    ]
                ],
                'categories' => $bySecretariat->pluck('secretariat_name')->toArray()
            ];
        }

        // Gráfico de Gastos do Mês (últimos 7 dias) - CORREÇÃO APLICADA AQUI
        $startDate = now()->subDays(6)->startOfDay();
        $expenses = \App\Models\fuel\Fueling::query()
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('DATE(created_at) as date, SUM(liters * value_per_liter) as total') // Correção: Trocado total_cost por liters * value_per_liter
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($expenses->isNotEmpty()) {
            $expensesData = [
                'series' => [
                    [
                        'name' => 'Gastos (R$)',
                        'data' => $expenses->pluck('total')->map(fn($v) => round($v, 2))->toArray()
                    ]
                ],
                'categories' => $expenses->map(fn($e) => \Carbon\Carbon::parse($e->date)->format('d/m'))->toArray()
            ];
        }

        // Gráfico de Abastecimentos Recentes (últimos 7 dias)
        $fuelings = \App\Models\fuel\Fueling::query()
            ->whereBetween('created_at', [$startDate, now()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(liters) as liters')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($fuelings->isNotEmpty()) {
            $fuelingsData = [
                'series' => [
                    [
                        'name' => 'Quantidade',
                        'data' => $fuelings->pluck('total')->toArray()
                    ],
                    [
                        'name' => 'Litros',
                        'data' => $fuelings->pluck('liters')->map(fn($v) => round($v, 2))->toArray()
                    ]
                ],
                'categories' => $fuelings->map(fn($f) => \Carbon\Carbon::parse($f->date)->format('d/m'))->toArray()
            ];
        }
    }

    return view('dashboard', compact('vehiclesInUse', 'stats', 'chartData', 'expensesData', 'fuelingsData'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/remove-photo', [ProfileController::class, 'removeProfilePhoto'])->name('profile.remove-photo');
    Route::delete('/profile/remove-cnh-photo', [ProfileController::class, 'removeCnhPhoto'])->name('profile.remove-cnh-photo');

    // Backup Reports
    Route::get('/backup-reports', [BackupReportController::class, 'index'])->name('backup-reports.index');
    Route::get('/backup-reports/{backupReport}/download', [BackupReportController::class, 'download'])->name('backup-reports.download');
    Route::delete('/backup-reports/{backupReport}', [BackupReportController::class, 'destroy'])->name('backup-reports.destroy');

    // PDF Templates (apenas role 1)
    Route::resource('pdf-templates', \App\Http\Controllers\PdfTemplateController::class);
    Route::post('/pdf-templates/preview', [\App\Http\Controllers\PdfTemplateController::class, 'preview'])->name('pdf-templates.preview');

    // Painel de Veículos em Uso (apenas para gestores) - CORREÇÃO DE POSIÇÃO DA ROTA
    Route::get('/vehicles/usage-panel', [\App\Http\Controllers\vehicle\VehiclesUsagePanelController::class, 'index'])
        ->middleware(['auth', 'verified', 'role:general_manager,sector_manager'])
        ->name('vehicles.usage-panel');
// Vehicle Price Origins (Patrimônio dos Veículos)
    Route::resource('vehicle-price-origins', \App\Http\Controllers\vehicle\VehiclePriceOriginController::class)->except(['destroy']);

// API para buscar veículos disponíveis para patrimônio
    Route::get('/api/vehicle-price-origins/available-vehicles', [\App\Http\Controllers\vehicle\VehiclePriceOriginController::class, 'searchAvailableVehicles'])->name('api.vehicle-price-origins.available-vehicles');

    Route::resource('vehicles', VehicleController::class);
    Route::resource('vehicle-categories', VehicleCategoryController::class);
    Route::resource('prefixes', PrefixController::class);

    // API Routes para prefixos
    Route::get('/api/prefixes/search', [PrefixController::class, 'search'])->name('api.prefixes.search');
    Route::post('/api/prefixes/store-inline', [PrefixController::class, 'storeInline'])->name('api.prefixes.store-inline');

    // API Routes para veículos
    Route::get('/api/vehicles/search', [VehicleController::class, 'search'])->name('api.vehicles.search');
    Route::get('/api/vehicles/{vehicle}/data', [\App\Http\Controllers\runs\RunController::class, 'getVehicleData'])->name('api.vehicles.data');
    Route::resource('defect-reports', DefectReportController::class)->except(['edit', 'update', 'destroy']);


    // API Routes para usuários
    Route::get('/api/users/search', [UserController::class, 'search'])->name('api.users.search');

    // API Routes para secretarias
    Route::get('/api/secretariats/search', [\App\Http\Controllers\users\SecretariatController::class, 'search'])->name('api.secretariats.search');

    // Rotas do Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ChatController::class, 'index'])->name('index');
        Route::get('/search-users', [\App\Http\Controllers\ChatController::class, 'searchUsers'])->name('search-users');
        Route::get('/room/{chatRoom}/messages', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
        Route::post('/room/{chatRoom}/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');
        Route::post('/room/{chatRoom}/mark-read', [\App\Http\Controllers\ChatController::class, 'markAsRead'])->name('mark-read');
        Route::post('/room/{chatRoom}/typing', [\App\Http\Controllers\ChatController::class, 'typing'])->name('typing');
        Route::post('/room/{chatRoom}/upload', [\App\Http\Controllers\ChatController::class, 'uploadAttachment'])->name('upload');
        Route::get('/start/{user}', [\App\Http\Controllers\ChatController::class, 'getOrCreatePrivateChat'])->name('start');
        Route::post('/group/create', [\App\Http\Controllers\ChatController::class, 'createGroup'])->name('group.create');
    });

    // Mensagens em Massa (apenas para general_manager e sector_manager)
    Route::prefix('broadcast-messages')->name('broadcast-messages.')->middleware('auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\BroadcastMessageController::class, 'index'])->name('index');
        Route::post('/send-individual', [\App\Http\Controllers\BroadcastMessageController::class, 'sendIndividual'])->name('send-individual');
        Route::post('/create-group', [\App\Http\Controllers\BroadcastMessageController::class, 'createGroup'])->name('create-group');
        Route::post('/users-by-secretariat', [\App\Http\Controllers\BroadcastMessageController::class, 'getUsersBySecretariat'])->name('users-by-secretariat');
    });

    Route::resource('users', UserController::class);
    Route::resource('default-passwords', DefaultPasswordController::class);

    // Audit Logs (apenas para gestores gerais)
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');

    // Logbook Permissions (apenas para gestores gerais)
    Route::resource('logbook-permissions', \App\Http\Controllers\runs\LogbookPermissionController::class);

    // Vehicle Transfers (Transferências de Veículos)
    Route::prefix('vehicle-transfers')->name('vehicle-transfers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'store'])->name('store');
        Route::get('/pending', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'pending'])->name('pending');
        Route::get('/active', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'active'])->name('active');
        Route::get('/{vehicleTransfer}', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'show'])->name('show');
        Route::post('/{vehicleTransfer}/approve', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'approve'])->name('approve');
        Route::post('/{vehicleTransfer}/reject', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'reject'])->name('reject');
        Route::post('/{vehicleTransfer}/return', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'return'])->name('return');
    });

    // API para buscar veículos na transferência
    Route::get('/api/vehicle-transfers/search-vehicle', [\App\Http\Controllers\vehicle\VehicleTransferController::class, 'searchVehicle'])->name('api.vehicle-transfers.search-vehicle');

    // Diário de Bordo (Logbook)
    Route::prefix('logbook')->name('logbook.')->group(function () {
        Route::get('/', [\App\Http\Controllers\runs\RunController::class, 'index'])->name('index');
        Route::get('/start', [\App\Http\Controllers\runs\RunController::class, 'start'])->name('start-flow');

        // Etapa 1: Seleção do Veículo
        Route::get('/select-vehicle', [\App\Http\Controllers\runs\RunController::class, 'selectVehicle'])->name('vehicle-select');
        Route::post('/select-vehicle', [\App\Http\Controllers\runs\RunController::class, 'storeVehicle'])->name('store-vehicle');

        // Etapa 2: Checklist (NOVO FLUXO - sem corrida criada)
        Route::get('/checklist_form', [\App\Http\Controllers\runs\RunController::class, 'checklistForm'])->name('checklist-form');
        Route::post('/checklist_form', [\App\Http\Controllers\runs\RunController::class, 'storeChecklistAndCreateRun'])->name('store-checklist-form');

        // Etapa 2: Checklist (ANTIGO - para compatibilidade)
        Route::get('/{run}/checklist', [\App\Http\Controllers\runs\RunController::class, 'checklist'])->name('checklist');
        Route::post('/{run}/checklist', [\App\Http\Controllers\runs\RunController::class, 'storeChecklist'])->name('store-checklist');

        // Etapa 3: Iniciar Corrida
        Route::get('/{run}/start-run', [\App\Http\Controllers\runs\RunController::class, 'startRun'])->name('start-run');
        Route::post('/{run}/start-run', [\App\Http\Controllers\runs\RunController::class, 'storeStartRun'])->name('store-start-run');

        // Etapa 4: Finalizar Corrida
        Route::get('/{run}/finish', [\App\Http\Controllers\runs\RunController::class, 'finishRun'])->name('finish');
        Route::post('/{run}/finish', [\App\Http\Controllers\runs\RunController::class, 'storeFinishRun'])->name('store-finish');

        // Etapa 5: Abastecimento (Opcional)
        Route::get('/{run}/fueling', [\App\Http\Controllers\fuel\FuelingRecordController::class, 'create'])->name('fueling');
        Route::post('/{run}/fueling', [\App\Http\Controllers\fuel\FuelingRecordController::class, 'store'])->name('store-fueling');

        // Cancelar corrida
        Route::delete('/{run}/cancel', [\App\Http\Controllers\runs\RunController::class, 'cancel'])->name('cancel');

        // Detalhes da corrida
        Route::get('/{run}', [\App\Http\Controllers\runs\RunController::class, 'show'])->name('show');
    });

    Route::get('/api/vehicle-categories', [VehicleCategoryController::class, 'apiIndex'])->name('api.vehicle-categories.search');
    Route::get('/api/vehicles', [VehicleController::class, 'apiSearch'])->name('api.vehicles.search');
    Route::get('/api/users', [UserController::class, 'search'])->name('api.users.search');

    Route::post('/check-duplicate-rule', function (Request $request) {
        $targetType = $request->target_type;
        $targetId = $request->target_id;
        $currentRuleId = $request->current_rule_id;

        \Log::info("API Check: target_type={$targetType}, target_id={$targetId}, current_rule_id={$currentRuleId}");

        $query = \App\Models\logbook\LogbookRule::where('target_type', $targetType)
            ->where('is_active', true);

        if ($targetType === 'global') {
            $query->whereNull('target_id');
        } else {
            $query->where('target_id', $targetId);
        }

        if ($currentRuleId) {
            $query->where('id', '!=', $currentRuleId);
        }

        $exists = $query->exists();
        $existingRule = $query->first();

        \Log::info("API Check Result: exists={$exists}, rule_id=" . ($existingRule ? $existingRule->id : 'null'));

        return response()->json([
            'valid' => !$exists,
            'exists' => $exists,
            'message' => $exists ? 'Já existe uma regra ativa para este alvo.' : 'Alvo disponível.',
            'existing_rule_id' => $existingRule ? $existingRule->id : null
        ]);
    });

    Route::prefix('logbook-rules')->name('logbook-rules.')->group(function () {
        Route::get('/', [LogbookRuleController::class, 'index'])->name('index');
        Route::get('/create', [LogbookRuleController::class, 'create'])->name('create');
        Route::post('/', [LogbookRuleController::class, 'store'])->name('store');
        Route::get('/{logbookRule}/edit', [LogbookRuleController::class, 'edit'])->name('edit');
        Route::put('/{logbookRule}', [LogbookRuleController::class, 'update'])->name('update');
        Route::delete('/{logbookRule}', [LogbookRuleController::class, 'destroy'])->name('destroy');
        Route::patch('/{logbookRule}/toggle-status', [LogbookRuleController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Checklists (Notificações e Aprovações)
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/', [\App\Http\Controllers\runs\ChecklistController::class, 'index'])->name('index');
        Route::get('/pending', [\App\Http\Controllers\runs\ChecklistController::class, 'pending'])->name('pending');
        Route::get('/{checklist}', [\App\Http\Controllers\runs\ChecklistController::class, 'show'])->name('show');
        Route::post('/{checklist}/approve', [\App\Http\Controllers\runs\ChecklistController::class, 'approve'])->name('approve');
        Route::post('/{checklist}/reject', [\App\Http\Controllers\runs\ChecklistController::class, 'reject'])->name('reject');
    });

    // MÓDULO DE PNEUS
    Route::middleware(['auth'])->prefix('tires')->name('tires.')->group(function () {
        // Dashboard
        Route::get('/', [\App\Http\Controllers\maintenance\TireController::class, 'index'])->name('index');
        // Gestão de Veículos
        Route::get('/vehicles', [\App\Http\Controllers\maintenance\TireController::class, 'vehicles'])->name('vehicles');
        Route::get('/vehicles/{vehicle}', [\App\Http\Controllers\maintenance\TireController::class, 'showVehicle'])->name('vehicles.show');

        // Estoque de Pneus
        Route::get('/stock', [\App\Http\Controllers\maintenance\TireController::class, 'stock'])->name('stock');
        Route::get('/create', [\App\Http\Controllers\maintenance\TireController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\maintenance\TireController::class, 'store'])->name('store');

        // Ações de Manutenção
        Route::post('/rotate', [\App\Http\Controllers\maintenance\TireController::class, 'rotate'])->name('rotate');
        Route::post('/replace', [\App\Http\Controllers\maintenance\TireController::class, 'replace'])->name('replace');
        Route::post('/remove', [\App\Http\Controllers\maintenance\TireController::class, 'remove'])->name('remove');
        Route::post('/register-event', [\App\Http\Controllers\maintenance\TireController::class, 'registerEvent'])->name('register-event');

        // Histórico
        Route::get('/history/{tire}', [\App\Http\Controllers\maintenance\TireController::class, 'history'])->name('history');

        // Atualizar condição
        Route::post('/{tire}/update-condition', [\App\Http\Controllers\maintenance\TireController::class, 'updateCondition'])->name('update-condition');
    });

    // API para pneus
    Route::get('/api/tires/{tire}', function($id) {
        return \App\Models\maintenance\Tire::findOrFail($id);
    })->middleware('auth');

    // Manutenção - Troca de Óleo
    Route::prefix('oil-changes')->name('oil-changes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\maintenance\OilChangeController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\maintenance\OilChangeController::class, 'store'])->name('store');
        Route::get('/vehicle/{vehicle}/history', [\App\Http\Controllers\maintenance\OilChangeController::class, 'history'])->name('history');
        Route::get('/settings', [\App\Http\Controllers\maintenance\OilChangeController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\maintenance\OilChangeController::class, 'storeSettings'])->name('settings.store');
        Route::put('/settings/{setting}', [\App\Http\Controllers\maintenance\OilChangeController::class, 'updateSettings'])->name('settings.update');
    });

    // API para dados do veículo (troca de óleo)
    Route::get('/api/oil-changes/vehicle-data/{vehicle}', [\App\Http\Controllers\maintenance\OilChangeController::class, 'getVehicleData'])->name('api.oil-changes.vehicle-data');

    // Postos de Combustível
    Route::resource('gas-stations', \App\Http\Controllers\fuel\GasStationController::class);
    Route::get('/api/gas-stations/search', [\App\Http\Controllers\fuel\GasStationController::class, 'search'])->name('api.gas-stations.search');
    Route::post('/gas-stations/check-cnpj', [GasStationController::class, 'checkCnpj'])->name('gas-stations.check-cnpj');
    //***** INICIO DO AGENDAMENTOS DE POSTOS *****//

    // Rotas para o CRUD de Agendamento de Postos
    Route::resource('scheduled_gas_stations', ScheduledGasStationController::class);

    // Rota para a visualização dos Postos Atuais
    Route::get('gas_stations_current', [GasStationCurrentController::class, 'index'])->name('gas_stations_current.index');

    // Rotas para o CRUD de Agendamento de Preços
    Route::resource('scheduled_prices', ScheduledPriceController::class);

    // Rota para a visualização dos Preços Atuais
    Route::get('fuel_prices', [FuelPriceController::class, 'index'])->name('fuel_prices.index');
    //***** FIM DO AGENDAMENTOS DE POSTOS *****//

    // Cotação de Combustível
    Route::prefix('fuel-quotations')->name('fuel-quotations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'store'])->name('store');
        Route::get('/{fuelQuotation}', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'show'])->name('show');
        Route::get('/{fuelQuotation}/edit', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'edit'])->name('edit');
        Route::put('/{fuelQuotation}', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'update'])->name('update');
        Route::delete('/{fuelQuotation}', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'destroy'])->name('destroy');

        // API
        Route::post('/calculate-averages', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'calculateAverages'])->name('calculate-averages');
        Route::post('/delete-image', [\App\Http\Controllers\fuel\FuelQuotationController::class, 'deleteImage'])->name('delete-image');

        // Configurações (apenas para gestores gerais)
        Route::get('/settings', [\App\Http\Controllers\fuel\FuelQuotationSettingsController::class, 'index'])->name('settings')->middleware('can:isGeneralManager');
        Route::post('/settings/calculation-methods', [\App\Http\Controllers\fuel\FuelQuotationSettingsController::class, 'storeCalculationMethod'])->name('settings.calculation-methods.store');
        Route::put('/settings/calculation-methods/{method}', [\App\Http\Controllers\fuel\FuelQuotationSettingsController::class, 'updateCalculationMethod'])->name('settings.calculation-methods.update');
        Route::delete('/settings/calculation-methods/{method}', [\App\Http\Controllers\fuel\FuelQuotationSettingsController::class, 'destroyCalculationMethod'])->name('settings.calculation-methods.destroy');
        Route::post('/settings/discount-settings', [\App\Http\Controllers\fuel\FuelQuotationSettingsController::class, 'storeDiscountSetting'])->name('settings.discount-settings.store');
        Route::put('/settings/discount-settings/{discount}', [\App\Http\Controllers\fuel\FuelQuotationSettingsController::class, 'updateDiscountSetting'])->name('settings.discount-settings.update');
        Route::delete('/settings/discount-settings/{discount}', [\App\Http\Controllers\fuel\FuelQuotationSettingsController::class, 'destroyDiscountSetting'])->name('settings.discount-settings.destroy');

    });

    // Multas
    Route::prefix('fines')->name('fines.')->group(function () {
        Route::get('/', [\App\Http\Controllers\FineController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\FineController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\FineController::class, 'store'])->name('store');
        Route::get('/{fine}', [\App\Http\Controllers\FineController::class, 'show'])->name('show');
        Route::get('/{fine}/edit', [\App\Http\Controllers\FineController::class, 'edit'])->name('edit');
        Route::put('/{fine}', [\App\Http\Controllers\FineController::class, 'update'])->name('update');
        Route::delete('/{fine}', [\App\Http\Controllers\FineController::class, 'destroy'])->name('destroy');
        Route::patch('/{fine}/status', [\App\Http\Controllers\FineController::class, 'updateStatus'])->name('update-status');
        Route::get('/{fine}/pdf', [\App\Http\Controllers\FineController::class, 'generatePdf'])->name('pdf');
    });

    // API para busca de multas
    Route::get('/api/fines/search-vehicles', [\App\Http\Controllers\FineController::class, 'searchVehicles'])->name('api.fines.search-vehicles');
    Route::get('/api/fines/search-drivers', [\App\Http\Controllers\FineController::class, 'searchDrivers'])->name('api.fines.search-drivers');
    Route::get('/api/fines/search-notices', [\App\Http\Controllers\FineController::class, 'searchInfractionNotices'])->name('api.fines.search-notices');

}); // <--- ESTA É A CHAVE DE FECHAMENTO CORRETA PARA O GRUPO DE AUTH

// Verificação de Autenticidade de Multas (Público)
Route::get('/fines/verify', function() {
    return view('fines.verify');
})->name('fines.verify.form');
Route::post('/fines/verify', [\App\Http\Controllers\FineController::class, 'verify'])->name('fines.verify');

require __DIR__.'/auth.php';
