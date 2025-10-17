<?php

namespace App\Http\Controllers;

use App\Models\defect\DefaultPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DefaultPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // Apenas gestores gerais podem gerenciar senhas padrão
        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para gerenciar senhas padrão.');
        }

        $search = $request->input('search');

        $passwords = DefaultPassword::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('default-passwords.index', compact('passwords', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUser = auth()->user();

        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para criar senhas padrão.');
        }

        return view('default-passwords.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para criar senhas padrão.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:default_passwords,name'],
            'password' => ['required', 'string', 'min:8'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        DefaultPassword::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('default-passwords.index')
            ->with('success', 'Senha padrão criada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DefaultPassword $defaultPassword)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para editar senhas padrão.');
        }

        return view('default-passwords.edit', compact('defaultPassword'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DefaultPassword $defaultPassword)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para editar senhas padrão.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:default_passwords,name,' . $defaultPassword->id],
            'password' => ['nullable', 'string', 'min:8'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $defaultPassword->update($data);

        return redirect()->route('default-passwords.index')
            ->with('success', 'Senha padrão atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DefaultPassword $defaultPassword)
    {
        $currentUser = auth()->user();

        if (!$currentUser->isGeneralManager()) {
            abort(403, 'Você não tem permissão para excluir senhas padrão.');
        }

        $defaultPassword->delete();

        return redirect()->route('default-passwords.index')
            ->with('success', 'Senha padrão excluída com sucesso.');
    }
}
