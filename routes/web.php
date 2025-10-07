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

    Route::resource('users', UserController::class);
    Route::resource('default-passwords', DefaultPasswordController::class);

    // Audit Logs (apenas para gestores gerais)
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
});



require __DIR__.'/auth.php';
