<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleCategoryController;
use App\Http\Controllers\PrefixController;
use App\Http\Controllers\BackupReportController;

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
    Route::resource('users', UserController::class);
});



require __DIR__.'/auth.php';
