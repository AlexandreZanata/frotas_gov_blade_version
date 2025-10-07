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
        // Usamos with() para carregar os relacionamentos e evitar o problema N+1
        $users = User::with(['role', 'secretariat'])->latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Mostra o formulário para criar um novo usuário.
     */
    public function create()
    {
        $roles = Role::all();
        $secretariats = Secretariat::all();

        return view('users.create', compact('roles', 'secretariats'));
    }

    /**
     * Salva um novo usuário no banco de dados.
     */
    public function store(Request $request)
    {
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
        $roles = Role::all();
        $secretariats = Secretariat::all();

        return view('users.edit', compact('user', 'roles', 'secretariats'));
    }

    /**
     * Atualiza um usuário no banco de dados.
     */
    public function update(Request $request, User $user)
    {
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
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
