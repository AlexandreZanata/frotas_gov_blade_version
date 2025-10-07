<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Secretariat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Exibe a lista de usuários.
     */
    public function index()
    {
        $currentUser = auth()->user();

        // Gestor geral vê todos os usuários
        if ($currentUser->isGeneralManager()) {
            $users = User::with(['role', 'secretariat'])->latest()->paginate(15);
        }
        // Gestor setorial vê apenas usuários da sua secretaria
        elseif ($currentUser->isSectorManager()) {
            $users = User::with(['role', 'secretariat'])
                ->where('secretariat_id', $currentUser->secretariat_id)
                ->latest()
                ->paginate(15);
        }
        // Outros usuários não podem ver listagem
        else {
            abort(403, 'Você não tem permissão para visualizar usuários.');
        }

        return view('users.index', compact('users'));
    }

    /**
     * Mostra o formulário para criar um novo usuário.
     */
    public function create()
    {
        $currentUser = auth()->user();

        // Apenas gestores podem criar usuários
        if (!$currentUser->isManager()) {
            abort(403, 'Você não tem permissão para criar usuários.');
        }

        $secretariats = Secretariat::all();

        // Gestor geral pode atribuir qualquer role
        if ($currentUser->isGeneralManager()) {
            $roles = Role::all();
        }
        // Gestor setorial só pode criar motoristas e mecânicos
        else {
            $roles = Role::whereIn('name', ['driver', 'mechanic'])->get();
        }

        return view('users.create', compact('roles', 'secretariats'));
    }

    /**
     * Salva um novo usuário no banco de dados.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();

        // Apenas gestores podem criar usuários
        if (!$currentUser->isManager()) {
            abort(403, 'Você não tem permissão para criar usuários.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'cpf' => ['required', 'string', 'max:14', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'secretariat_id' => ['required', 'exists:secretariats,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'cnh' => ['nullable', 'string', 'max:20'],
            'cnh_expiration_date' => ['nullable', 'date'],
            'cnh_category' => ['nullable', 'string', 'max:5'],
        ]);

        // Verificar se o gestor setorial está tentando criar role que não pode
        $targetRole = Role::findOrFail($request->role_id);
        if ($currentUser->isSectorManager() && !in_array($targetRole->name, ['driver', 'mechanic'])) {
            abort(403, 'Você só pode criar usuários com roles de Motorista ou Mecânico.');
        }

        // Verificar se está criando usuário em outra secretaria (gestor setorial)
        if ($currentUser->isSectorManager() && $request->secretariat_id != $currentUser->secretariat_id) {
            abort(403, 'Você só pode criar usuários na sua própria secretaria.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'secretariat_id' => $request->secretariat_id,
            'phone' => $request->phone,
            'cnh' => $request->cnh,
            'cnh_expiration_date' => $request->cnh_expiration_date,
            'cnh_category' => $request->cnh_category,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    /**
     * Mostra o formulário para editar um usuário existente.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();

        // Verificar se pode gerenciar este usuário
        if (!$currentUser->canManage($user)) {
            abort(403, 'Você não tem permissão para editar este usuário.');
        }

        $secretariats = Secretariat::all();

        // Gestor geral pode atribuir qualquer role
        if ($currentUser->isGeneralManager()) {
            $roles = Role::all();
        }
        // Gestor setorial só pode atribuir motoristas e mecânicos
        else {
            $roles = Role::whereIn('name', ['driver', 'mechanic'])->get();
        }

        return view('users.edit', compact('user', 'roles', 'secretariats'));
    }

    /**
     * Atualiza um usuário no banco de dados.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Verificar se pode gerenciar este usuário
        if (!$currentUser->canManage($user)) {
            abort(403, 'Você não tem permissão para editar este usuário.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'cpf' => ['required', 'string', 'max:14', 'unique:users,cpf,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'],
            'secretariat_id' => ['required', 'exists:secretariats,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'cnh' => ['nullable', 'string', 'max:20'],
            'cnh_expiration_date' => ['nullable', 'date'],
            'cnh_category' => ['nullable', 'string', 'max:5'],
        ]);

        // Verificar se o gestor setorial está tentando atribuir role que não pode
        $targetRole = Role::findOrFail($request->role_id);
        if ($currentUser->isSectorManager() && !in_array($targetRole->name, ['driver', 'mechanic'])) {
            abort(403, 'Você só pode atribuir roles de Motorista ou Mecânico.');
        }

        // Verificar se está movendo usuário para outra secretaria (gestor setorial)
        if ($currentUser->isSectorManager() && $request->secretariat_id != $currentUser->secretariat_id) {
            abort(403, 'Você só pode gerenciar usuários na sua própria secretaria.');
        }

        $data = $request->except('password');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove um usuário do banco de dados.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        // Verificar se pode gerenciar este usuário
        if (!$currentUser->canManage($user)) {
            abort(403, 'Você não tem permissão para excluir este usuário.');
        }

        // Não pode excluir a si mesmo
        if ($currentUser->id === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir sua própria conta.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
