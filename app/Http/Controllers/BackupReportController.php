<?php

namespace App\Http\Controllers;

use App\Models\BackupReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $backups = BackupReport::with('user')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('entity_name', 'like', "%{$search}%")
                      ->orWhere('entity_type', 'like', "%{$search}%")
                      ->orWhere('file_name', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('backup-reports.index', compact('backups', 'search'));
    }

    public function download(BackupReport $backupReport)
    {
        if (!Storage::disk('local')->exists($backupReport->file_path)) {
            abort(404, 'Arquivo de backup não encontrado.');
        }

        return response()->download(
            Storage::disk('local')->path($backupReport->file_path),
            $backupReport->file_name,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $backupReport->file_name . '"'
            ]
        );
    }

    public function destroy(BackupReport $backupReport)
    {
        // Deletar arquivo físico
        if (Storage::disk('local')->exists($backupReport->file_path)) {
            Storage::disk('local')->delete($backupReport->file_path);
        }

        $backupReport->delete();

        return redirect()->route('backup-reports.index')
            ->with('success', 'Relatório de backup excluído com sucesso.');
    }
}
