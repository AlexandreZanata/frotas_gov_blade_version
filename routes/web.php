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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
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

    Route::resource('vehicles', VehicleController::class);
    Route::resource('vehicle-categories', VehicleCategoryController::class);
    Route::resource('prefixes', PrefixController::class);

    // API Routes para prefixos
    Route::get('/api/prefixes/search', [PrefixController::class, 'search'])->name('api.prefixes.search');
    Route::post('/api/prefixes/store-inline', [PrefixController::class, 'storeInline'])->name('api.prefixes.store-inline');

    // API Routes para veículos
    Route::get('/api/vehicles/search', [VehicleController::class, 'search'])->name('api.vehicles.search');
    Route::get('/api/vehicles/{vehicle}/data', [\App\Http\Controllers\RunController::class, 'getVehicleData'])->name('api.vehicles.data');

    Route::resource('users', UserController::class);
    Route::resource('default-passwords', DefaultPasswordController::class);

    // Audit Logs (apenas para gestores gerais)
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');

    // Diário de Bordo (Logbook)
    Route::prefix('logbook')->name('logbook.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RunController::class, 'index'])->name('index');
        Route::get('/start', [\App\Http\Controllers\RunController::class, 'start'])->name('start');

        // Etapa 1: Seleção do Veículo
        Route::get('/select-vehicle', [\App\Http\Controllers\RunController::class, 'selectVehicle'])->name('vehicle-select');
        Route::post('/select-vehicle', [\App\Http\Controllers\RunController::class, 'storeVehicle'])->name('store-vehicle');

        // Etapa 2: Checklist (NOVO FLUXO - sem corrida criada)
        Route::get('/checklist-form', [\App\Http\Controllers\RunController::class, 'checklistForm'])->name('checklist-form');
        Route::post('/checklist-form', [\App\Http\Controllers\RunController::class, 'storeChecklistAndCreateRun'])->name('store-checklist-form');

        // Etapa 2: Checklist (ANTIGO - para compatibilidade)
        Route::get('/{run}/checklist', [\App\Http\Controllers\RunController::class, 'checklist'])->name('checklist');
        Route::post('/{run}/checklist', [\App\Http\Controllers\RunController::class, 'storeChecklist'])->name('store-checklist');

        // Etapa 3: Iniciar Corrida
        Route::get('/{run}/start-run', [\App\Http\Controllers\RunController::class, 'startRun'])->name('start');
        Route::post('/{run}/start-run', [\App\Http\Controllers\RunController::class, 'storeStartRun'])->name('store-start');

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
});



require __DIR__.'/auth.php';
