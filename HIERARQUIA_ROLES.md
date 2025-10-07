# Sistema de Hierarquia de Roles - Documentação

## Visão Geral

O sistema implementa um controle de acesso baseado em hierarquia de roles, onde:

- **General Manager (Gestor Geral)**: Nível 100 - Acesso total ao sistema
- **Sector Manager (Gestor Setorial)**: Nível 50 - Gerencia usuários da sua secretaria
- **Driver (Motorista)**: Nível 10 - Acesso limitado
- **Mechanic (Mecânico)**: Nível 10 - Acesso limitado

## Estrutura do Banco de Dados

### Tabela `roles`
```sql
- id (UUID)
- name (string, unique)
- description (string, nullable)
- hierarchy_level (integer) - Quanto maior, mais privilégios
- timestamps
```

## Métodos Disponíveis

### No Model User

#### Verificação de Role Específica
```php
$user->hasRole('general_manager'); // bool
$user->hasAnyRole(['driver', 'mechanic']); // bool
```

#### Verificação de Tipo de Usuário
```php
$user->isGeneralManager(); // bool
$user->isSectorManager(); // bool
$user->isDriver(); // bool
$user->isMechanic(); // bool
$user->isManager(); // bool - Retorna true para general_manager ou sector_manager
```

#### Verificação de Hierarquia
```php
// Verificar se tem hierarquia maior ou igual a outro usuário
$user->hasHigherOrEqualHierarchyThan($otherUser); // bool

// Verificar se tem hierarquia maior que outro usuário
$user->hasHigherHierarchyThan($otherUser); // bool

// Verificar se pode gerenciar outro usuário (baseado em hierarquia e secretaria)
$user->canManage($otherUser); // bool

// Obter nível hierárquico
$user->getHierarchyLevel(); // int
```

#### Relacionamentos
```php
$user->role; // Retorna o objeto Role
$user->secretariat; // Retorna o objeto Secretariat
```

### No Model Role

```php
// Verificar hierarquia entre roles
$role->hasHigherOrEqualHierarchyThan($otherRole); // bool
$role->hasHigherHierarchyThan($otherRole); // bool

// Obter usuários com esta role
$role->users; // Collection de User
```

## Regras de Negócio Implementadas

### General Manager (Gestor Geral)
- ✅ Pode visualizar todos os usuários do sistema
- ✅ Pode criar usuários com qualquer role
- ✅ Pode editar qualquer usuário
- ✅ Pode excluir qualquer usuário (exceto a si mesmo)
- ✅ Pode atribuir usuários a qualquer secretaria

### Sector Manager (Gestor Setorial)
- ✅ Pode visualizar apenas usuários da sua secretaria
- ✅ Pode criar apenas Motoristas e Mecânicos
- ✅ Pode criar usuários apenas na sua secretaria
- ✅ Pode editar apenas Motoristas e Mecânicos da sua secretaria
- ✅ Pode excluir apenas Motoristas e Mecânicos da sua secretaria
- ❌ NÃO pode criar outros Gestores
- ❌ NÃO pode gerenciar usuários de outras secretarias

### Driver (Motorista) e Mechanic (Mecânico)
- ❌ NÃO podem visualizar lista de usuários
- ❌ NÃO podem criar usuários
- ❌ NÃO podem editar usuários
- ❌ NÃO podem excluir usuários

## Exemplos de Uso

### No Controller

```php
use Illuminate\Support\Facades\Auth;

class ExemploController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verificar se é gestor
        if ($user->isManager()) {
            // Lógica para gestores
        }
        
        // Verificar hierarquia antes de permitir ação
        $targetUser = User::find($id);
        if (!$user->canManage($targetUser)) {
            abort(403, 'Sem permissão');
        }
    }
}
```

### Nas Views Blade

```blade
@if(auth()->user()->isGeneralManager())
    <a href="{{ route('users.create') }}">Criar Gestor</a>
@endif

@if(auth()->user()->isManager())
    <a href="{{ route('users.create') }}">Criar Usuário</a>
@endif

@if(auth()->user()->canManage($targetUser))
    <a href="{{ route('users.edit', $targetUser) }}">Editar</a>
@endif
```

### Usando Middleware

Registre o middleware em `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRoleHierarchy::class,
    ]);
})
```

Use nas rotas em `routes/web.php`:

```php
// Apenas gestores gerais
Route::middleware(['auth', 'role:general_manager'])->group(function () {
    Route::get('/admin/settings', [SettingsController::class, 'index']);
});

// Gestores gerais ou setoriais
Route::middleware(['auth', 'role:general_manager,sector_manager'])->group(function () {
    Route::resource('users', UserController::class);
});

// Apenas motoristas
Route::middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/my-runs', [RunController::class, 'myRuns']);
});
```

### Queries com Hierarquia

```php
// Buscar apenas usuários que o usuário atual pode gerenciar
if ($currentUser->isGeneralManager()) {
    $users = User::all();
} elseif ($currentUser->isSectorManager()) {
    $users = User::where('secretariat_id', $currentUser->secretariat_id)
        ->whereHas('role', function($query) {
            $query->whereIn('name', ['driver', 'mechanic']);
        })
        ->get();
}

// Buscar roles que o usuário pode atribuir
if ($currentUser->isGeneralManager()) {
    $roles = Role::all();
} else {
    $roles = Role::whereIn('name', ['driver', 'mechanic'])->get();
}
```

## Testes

### Testar Hierarquia

```php
// No tinker ou em testes
$generalManager = User::whereHas('role', fn($q) => $q->where('name', 'general_manager'))->first();
$sectorManager = User::whereHas('role', fn($q) => $q->where('name', 'sector_manager'))->first();
$driver = User::whereHas('role', fn($q) => $q->where('name', 'driver'))->first();

$generalManager->canManage($sectorManager); // true
$generalManager->canManage($driver); // true
$sectorManager->canManage($driver); // true (se mesma secretaria)
$sectorManager->canManage($generalManager); // false
$driver->canManage($sectorManager); // false
```

## Comandos Importantes

### Rodar migrations e seeders
```bash
php artisan migrate:fresh --seed
```

### Verificar roles no banco
```bash
php artisan tinker
>>> Role::with('users')->get();
```

### Criar usuário via tinker
```bash
php artisan tinker
>>> $role = Role::where('name', 'general_manager')->first();
>>> User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'cpf' => '000.000.000-00',
    'password' => Hash::make('password'),
    'role_id' => $role->id,
    'secretariat_id' => Secretariat::first()->id,
]);
```

## Segurança

- Sempre verifique permissões no backend (Controller), não apenas no frontend
- Use o método `canManage()` antes de operações críticas
- O middleware `CheckRoleHierarchy` adiciona uma camada extra de segurança
- Nunca confie apenas em verificações do lado do cliente

## Migração de Dados Existentes

Se já tem usuários no banco:

```php
// Atribuir role padrão para usuários sem role
User::whereNull('role_id')->update([
    'role_id' => Role::where('name', 'driver')->first()->id
]);
```

