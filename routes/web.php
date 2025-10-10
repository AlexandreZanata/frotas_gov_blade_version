<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleCategoryController;
use App\Http\Controllers\PrefixController;
use App\Http\Controllers\BackupReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DefaultPasswordController;

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
        $query = \App\Models\Vehicle::query();

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
        $expenses = \App\Models\Fueling::query()
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
        $fuelings = \App\Models\Fueling::query()
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

    // Backup Reports
    Route::get('/backup-reports', [BackupReportController::class, 'index'])->name('backup-reports.index');
    Route::get('/backup-reports/{backupReport}/download', [BackupReportController::class, 'download'])->name('backup-reports.download');
    Route::delete('/backup-reports/{backupReport}', [BackupReportController::class, 'destroy'])->name('backup-reports.destroy');

    // PDF Templates (apenas role 1)
    Route::resource('pdf-templates', \App\Http\Controllers\PdfTemplateController::class);
    Route::post('/pdf-templates/preview', [\App\Http\Controllers\PdfTemplateController::class, 'preview'])->name('pdf-templates.preview');

    // Painel de Veículos em Uso (apenas para gestores) - CORREÇÃO DE POSIÇÃO DA ROTA
    Route::get('/vehicles/usage-panel', [\App\Http\Controllers\VehiclesUsagePanelController::class, 'index'])
        ->middleware(['auth', 'verified', 'role:general_manager,sector_manager'])
        ->name('vehicles.usage-panel');

    Route::resource('vehicles', VehicleController::class);
    Route::resource('vehicle-categories', VehicleCategoryController::class);
    Route::resource('prefixes', PrefixController::class);

    // API Routes para prefixos
    Route::get('/api/prefixes/search', [PrefixController::class, 'search'])->name('api.prefixes.search');
    Route::post('/api/prefixes/store-inline', [PrefixController::class, 'storeInline'])->name('api.prefixes.store-inline');

    // API Routes para veículos
    Route::get('/api/vehicles/search', [VehicleController::class, 'search'])->name('api.vehicles.search');
    Route::get('/api/vehicles/{vehicle}/data', [\App\Http\Controllers\RunController::class, 'getVehicleData'])->name('api.vehicles.data');

    // API Routes para usuários
    Route::get('/api/users/search', [UserController::class, 'search'])->name('api.users.search');

    // API Routes para secretarias
    Route::get('/api/secretariats/search', [\App\Http\Controllers\SecretariatController::class, 'search'])->name('api.secretariats.search');

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
    Route::resource('logbook-permissions', \App\Http\Controllers\LogbookPermissionController::class);

    // Vehicle Transfers (Transferências de Veículos)
    Route::prefix('vehicle-transfers')->name('vehicle-transfers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\VehicleTransferController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\VehicleTransferController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\VehicleTransferController::class, 'store'])->name('store');
        Route::get('/pending', [\App\Http\Controllers\VehicleTransferController::class, 'pending'])->name('pending');
        Route::get('/active', [\App\Http\Controllers\VehicleTransferController::class, 'active'])->name('active');
        Route::get('/{vehicleTransfer}', [\App\Http\Controllers\VehicleTransferController::class, 'show'])->name('show');
        Route::post('/{vehicleTransfer}/approve', [\App\Http\Controllers\VehicleTransferController::class, 'approve'])->name('approve');
        Route::post('/{vehicleTransfer}/reject', [\App\Http\Controllers\VehicleTransferController::class, 'reject'])->name('reject');
        Route::post('/{vehicleTransfer}/return', [\App\Http\Controllers\VehicleTransferController::class, 'return'])->name('return');
    });

    // API para buscar veículos na transferência
    Route::get('/api/vehicle-transfers/search-vehicle', [\App\Http\Controllers\VehicleTransferController::class, 'searchVehicle'])->name('api.vehicle-transfers.search-vehicle');

    // Diário de Bordo (Logbook)
    Route::prefix('logbook')->name('logbook.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RunController::class, 'index'])->name('index');
        Route::get('/start', [\App\Http\Controllers\RunController::class, 'start'])->name('start-flow');

        // Etapa 1: Seleção do Veículo
        Route::get('/select-vehicle', [\App\Http\Controllers\RunController::class, 'selectVehicle'])->name('vehicle-select');
        Route::post('/select-vehicle', [\App\Http\Controllers\RunController::class, 'storeVehicle'])->name('store-vehicle');

        // Etapa 2: Checklist (NOVO FLUXO - sem corrida criada)
        Route::get('/checklist_form', [\App\Http\Controllers\RunController::class, 'checklistForm'])->name('checklist-form');
        Route::post('/checklist_form', [\App\Http\Controllers\RunController::class, 'storeChecklistAndCreateRun'])->name('store-checklist-form');

        // Etapa 2: Checklist (ANTIGO - para compatibilidade)
        Route::get('/{run}/checklist', [\App\Http\Controllers\RunController::class, 'checklist'])->name('checklist');
        Route::post('/{run}/checklist', [\App\Http\Controllers\RunController::class, 'storeChecklist'])->name('store-checklist');

        // Etapa 3: Iniciar Corrida
        Route::get('/{run}/start-run', [\App\Http\Controllers\RunController::class, 'startRun'])->name('start-run');
        Route::post('/{run}/start-run', [\App\Http\Controllers\RunController::class, 'storeStartRun'])->name('store-start-run');

        // Etapa 4: Finalizar Corrida
        Route::get('/{run}/finish', [\App\Http\Controllers\RunController::class, 'finishRun'])->name('finish');
        Route::post('/{run}/finish', [\App\Http\Controllers\RunController::class, 'storeFinishRun'])->name('store-finish');

        // Etapa 5: Abastecimento (Opcional)
        Route::get('/{run}/fueling', [\App\Http\Controllers\RunController::class, 'fueling'])->name('fueling');
        Route::post('/{run}/fueling', [\App\Http\Controllers\RunController::class, 'storeFueling'])->name('store-fueling');

        // Cancelar corrida
        Route::delete('/{run}/cancel', [\App\Http\Controllers\RunController::class, 'cancel'])->name('cancel');

        // Detalhes da corrida
        Route::get('/{run}', [\App\Http\Controllers\RunController::class, 'show'])->name('show');
    });

    // Checklists (Notificações e Aprovações)
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ChecklistController::class, 'index'])->name('index');
        Route::get('/pending', [\App\Http\Controllers\ChecklistController::class, 'pending'])->name('pending');
        Route::get('/{checklist}', [\App\Http\Controllers\ChecklistController::class, 'show'])->name('show');
        Route::post('/{checklist}/approve', [\App\Http\Controllers\ChecklistController::class, 'approve'])->name('approve');
        Route::post('/{checklist}/reject', [\App\Http\Controllers\ChecklistController::class, 'reject'])->name('reject');
    });

    // MÓDULO DE PNEUS
    Route::middleware(['auth'])->prefix('tires')->name('tires.')->group(function () {
        // Dashboard
        Route::get('/', [\App\Http\Controllers\TireController::class, 'index'])->name('index');

        // Gestão de Veículos
        Route::get('/vehicles', [\App\Http\Controllers\TireController::class, 'vehicles'])->name('vehicles');
        Route::get('/vehicles/{vehicle}', [\App\Http\Controllers\TireController::class, 'showVehicle'])->name('vehicles.show');

        // Estoque de Pneus
        Route::get('/stock', [\App\Http\Controllers\TireController::class, 'stock'])->name('stock');
        Route::get('/create', [\App\Http\Controllers\TireController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\TireController::class, 'store'])->name('store');

        // Ações de Manutenção
        Route::post('/rotate', [\App\Http\Controllers\TireController::class, 'rotate'])->name('rotate');
        Route::post('/replace', [\App\Http\Controllers\TireController::class, 'replace'])->name('replace');
        Route::post('/remove', [\App\Http\Controllers\TireController::class, 'remove'])->name('remove');
        Route::post('/register-event', [\App\Http\Controllers\TireController::class, 'registerEvent'])->name('register-event');

        // Histórico
        Route::get('/history/{tire}', [\App\Http\Controllers\TireController::class, 'history'])->name('history');

        // Atualizar condição
        Route::post('/{tire}/update-condition', [\App\Http\Controllers\TireController::class, 'updateCondition'])->name('update-condition');
    });

    // API para pneus
    Route::get('/api/tires/{tire}', function($id) {
        return \App\Models\Tire::findOrFail($id);
    })->middleware('auth');

    // Manutenção - Troca de Óleo
    Route::prefix('oil-changes')->name('oil-changes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OilChangeController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\OilChangeController::class, 'store'])->name('store');
        Route::get('/vehicle/{vehicle}/history', [\App\Http\Controllers\OilChangeController::class, 'history'])->name('history');
        Route::get('/settings', [\App\Http\Controllers\OilChangeController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\OilChangeController::class, 'storeSettings'])->name('settings.store');
        Route::put('/settings/{setting}', [\App\Http\Controllers\OilChangeController::class, 'updateSettings'])->name('settings.update');
    });

    // API para dados do veículo (troca de óleo)
    Route::get('/api/oil-changes/vehicle-data/{vehicle}', [\App\Http\Controllers\OilChangeController::class, 'getVehicleData'])->name('api.oil-changes.vehicle-data');

    // Postos de Combustível
    Route::resource('gas-stations', \App\Http\Controllers\GasStationController::class);
    Route::get('/api/gas-stations/search', [\App\Http\Controllers\GasStationController::class, 'search'])->name('api.gas-stations.search');

    // Cotação de Combustível
    Route::prefix('fuel-quotations')->name('fuel-quotations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\FuelQuotationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\FuelQuotationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\FuelQuotationController::class, 'store'])->name('store');
        Route::get('/{fuelQuotation}', [\App\Http\Controllers\FuelQuotationController::class, 'show'])->name('show');
        Route::get('/{fuelQuotation}/edit', [\App\Http\Controllers\FuelQuotationController::class, 'edit'])->name('edit');
        Route::put('/{fuelQuotation}', [\App\Http\Controllers\FuelQuotationController::class, 'update'])->name('update');
        Route::delete('/{fuelQuotation}', [\App\Http\Controllers\FuelQuotationController::class, 'destroy'])->name('destroy');

        // API
        Route::post('/calculate-averages', [\App\Http\Controllers\FuelQuotationController::class, 'calculateAverages'])->name('calculate-averages');
        Route::post('/delete-image', [\App\Http\Controllers\FuelQuotationController::class, 'deleteImage'])->name('delete-image');

        // Configurações (apenas para gestores gerais)
        Route::get('/settings', [\App\Http\Controllers\FuelQuotationSettingsController::class, 'index'])->name('settings')->middleware('can:isGeneralManager');
        Route::post('/settings/calculation-methods', [\App\Http\Controllers\FuelQuotationSettingsController::class, 'storeCalculationMethod'])->name('settings.calculation-methods.store');
        Route::put('/settings/calculation-methods/{method}', [\App\Http\Controllers\FuelQuotationSettingsController::class, 'updateCalculationMethod'])->name('settings.calculation-methods.update');
        Route::delete('/settings/calculation-methods/{method}', [\App\Http\Controllers\FuelQuotationSettingsController::class, 'destroyCalculationMethod'])->name('settings.calculation-methods.destroy');
        Route::post('/settings/discount-settings', [\App\Http\Controllers\FuelQuotationSettingsController::class, 'storeDiscountSetting'])->name('settings.discount-settings.store');
        Route::put('/settings/discount-settings/{discount}', [\App\Http\Controllers\FuelQuotationSettingsController::class, 'updateDiscountSetting'])->name('settings.discount-settings.update');
        Route::delete('/settings/discount-settings/{discount}', [\App\Http\Controllers\FuelQuotationSettingsController::class, 'destroyDiscountSetting'])->name('settings.discount-settings.destroy');
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
