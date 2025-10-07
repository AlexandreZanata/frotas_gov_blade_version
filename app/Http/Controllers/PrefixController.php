<?php

namespace App\Http\Controllers;

use App\Models\Prefix;
use Illuminate\Http\Request;

class PrefixController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $prefixes = Prefix::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('prefixes.index', compact('prefixes', 'search'));
    }

    public function create()
    {
        return view('prefixes.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:prefixes,name']);
        Prefix::create($request->only('name'));
        return redirect()->route('prefixes.index')->with('success', 'Prefixo criado com sucesso.');
    }

    public function edit(Prefix $prefix)
    {
        return view('prefixes.edit', compact('prefix'));
    }

    public function update(Request $request, Prefix $prefix)
    {
        $request->validate(['name' => 'required|string|max:255|unique:prefixes,name,' . $prefix->id]);
        $prefix->update($request->only('name'));
        return redirect()->route('prefixes.index')->with('success', 'Prefixo atualizado com sucesso.');
    }

    public function destroy(Request $request, Prefix $prefix)
    {
        // Gerar backup se solicitado
        if ($request->has('create_backup') && $request->input('create_backup')) {
            try {
                $backupService = new \App\Services\BackupPdfService();
                $backupService->generatePrefixBackup($prefix);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Erro ao gerar backup: ' . $e->getMessage());
            }
        }

        $prefix->delete();

        return redirect()->route('prefixes.index')
            ->with('success', 'Prefixo excluído com sucesso.' . ($request->has('create_backup') ? ' Backup gerado com sucesso.' : ''));
    }
}
