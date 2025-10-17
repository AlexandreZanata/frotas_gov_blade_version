<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\user\Role;
use App\Models\user\Secretariat;
use App\Models\user\User;
use App\Rules\ValidCpf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $secretariats = Secretariat::orderBy('name')->get();
        return view('auth.register', compact('secretariats'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', new ValidCpf, 'unique:users,cpf'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'secretariat_id' => ['required', 'exists:secretariats,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'secretariat_id.required' => 'A secretaria é obrigatória.',
            'secretariat_id.exists' => 'Secretaria inválida.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'As senhas não conferem.',
        ]);

        // Remove formatação do CPF
        $cpf = preg_replace('/[^0-9]/', '', $validated['cpf']);

        // Busca o role padrão para novos usuários (driver = motorista)
        $defaultRole = Role::where('name', 'driver')->first();

        if (!$defaultRole) {
            // Fallback: pega o role com menor hierarquia
            $defaultRole = Role::orderBy('hierarchy_level', 'asc')->first();
        }

        $user = User::create([
            'name' => $validated['name'],
            'cpf' => $cpf,
            'email' => $validated['email'],
            'secretariat_id' => $validated['secretariat_id'],
            'role_id' => $defaultRole->id, // ADICIONA role_id padrão
            'password' => Hash::make($validated['password']),
            'status' => 'inactive', // SEMPRE INATIVO no registro
        ]);

        event(new Registered($user));

        // NÃO faz login automático - redireciona para tela de sucesso
        return redirect()->route('login')->with('status', 'Cadastro realizado com sucesso! Sua conta será ativada por um administrador.');
    }
}
