# 📚 GUIA COMPLETO: Como Aplicar Controle de Hierarquia em Controllers

## ✅ O que foi feito no Sidebar

O sidebar foi corrigido! Mudamos de:
```php
@if(auth()->user()->role_id == 1)  // ❌ ERRADO - usava ID numérico
```

Para:
```php
@if(auth()->user()->isGeneralManager())  // ✅ CORRETO - usa método helper
```

Agora o item "Modelos" só aparece para o **Gestor Geral**.

---

## 🎯 CONCEITOS FUNDAMENTAIS

### Hierarquia do Sistema
```
┌─────────────────────────────────────┐
│  GENERAL MANAGER (Gestor Geral)     │  Nível 100 - ACESSO TOTAL
│  ✅ Gerencia tudo e todos           │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│  SECTOR MANAGER (Gestor Setorial)   │  Nível 50 - ACESSO SETORIAL
│  ✅ Gerencia sua secretaria          │
└─────────────────────────────────────┘
            ↓
┌─────────────────────────────────────┐
│  DRIVER / MECHANIC                   │  Nível 10 - ACESSO BÁSICO
│  ❌ Sem permissões administrativas   │
└─────────────────────────────────────┘
```

### Métodos Disponíveis no User Model

```php
// 1. VERIFICAR ROLE ESPECÍFICA
$user->hasRole('general_manager')           // true/false
$user->hasAnyRole(['driver', 'mechanic'])   // true/false

// 2. VERIFICAR TIPO DE USUÁRIO
$user->isGeneralManager()    // É gestor geral?
$user->isSectorManager()     // É gestor setorial?
$user->isDriver()            // É motorista?
$user->isMechanic()          // É mecânico?
$user->isManager()           // É gestor (geral OU setorial)?

// 3. VERIFICAR HIERARQUIA
$user->hasHigherOrEqualHierarchyThan($otherUser)  // Hierarquia >= outro
$user->hasHigherHierarchyThan($otherUser)         // Hierarquia > outro
$user->canManage($otherUser)                      // Pode gerenciar outro?
$user->getHierarchyLevel()                        // Retorna número (100, 50, 10)
```

---

## 📝 PASSO A PASSO: Aplicar Hierarquia em Qualquer Controller

### PASSO 1: Importar Models Necessários

No topo do controller, certifique-se de ter:

```php
<?php

namespace App\Http\Controllers;

use App\Models\SeuModelo;  // O modelo principal
use App\Models\User;       // Para verificações de usuários
use App\Models\Role;       // Para buscar roles disponíveis
use Illuminate\Http\Request;
```

---

### PASSO 2: Método INDEX (Listagem)

**Objetivo:** Controlar quais registros o usuário pode ver.

```php
public function index()
{
    $currentUser = auth()->user();
    
    // GESTOR GERAL: Vê tudo
    if ($currentUser->isGeneralManager()) {
        $registros = SeuModelo::with('relacoes')->latest()->paginate(15);
    }
    // GESTOR SETORIAL: Vê apenas da sua secretaria
    elseif ($currentUser->isSectorManager()) {
        $registros = SeuModelo::with('relacoes')
            ->where('secretariat_id', $currentUser->secretariat_id)
            ->latest()
            ->paginate(15);
    }
    // DRIVER/MECHANIC: Vê apenas seus próprios registros
    else {
        $registros = SeuModelo::with('relacoes')
            ->where('user_id', $currentUser->id)
            ->latest()
            ->paginate(15);
    }

    return view('seu-modelo.index', compact('registros'));
}
```

**Variações Comuns:**

```php
// Se o modelo não tem secretariat_id, mas tem user_id:
elseif ($currentUser->isSectorManager()) {
    $registros = SeuModelo::with('relacoes')
        ->whereHas('user', function($query) use ($currentUser) {
            $query->where('secretariat_id', $currentUser->secretariat_id);
        })
        ->latest()
        ->paginate(15);
}

// Se apenas gestores podem ver a listagem:
if (!$currentUser->isManager()) {
    abort(403, 'Você não tem permissão para visualizar esta página.');
}
```

---

### PASSO 3: Método CREATE (Formulário de Criação)

**Objetivo:** Controlar quem pode criar e que opções vê.

```php
public function create()
{
    $currentUser = auth()->user();
    
    // Apenas gestores podem criar
    if (!$currentUser->isManager()) {
        abort(403, 'Você não tem permissão para criar registros.');
    }

    // GESTOR GERAL: Vê todas as opções
    if ($currentUser->isGeneralManager()) {
        $usuarios = User::all();
        $secretarias = Secretariat::all();
        $roles = Role::all();
    }
    // GESTOR SETORIAL: Vê opções limitadas
    else {
        // Apenas usuários da mesma secretaria
        $usuarios = User::where('secretariat_id', $currentUser->secretariat_id)->get();
        $secretarias = Secretariat::where('id', $currentUser->secretariat_id)->get();
        // Apenas roles básicas
        $roles = Role::whereIn('name', ['driver', 'mechanic'])->get();
    }

    return view('seu-modelo.create', compact('usuarios', 'secretarias', 'roles'));
}
```

---

### PASSO 4: Método STORE (Salvar Criação)

**Objetivo:** Validar permissões antes de salvar.

```php
public function store(Request $request)
{
    $currentUser = auth()->user();
    
    // Apenas gestores podem criar
    if (!$currentUser->isManager()) {
        abort(403, 'Você não tem permissão para criar registros.');
    }

    // Validação básica
    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id',
        'secretariat_id' => 'required|exists:secretariats,id',
        // ... outros campos
    ]);

    // VERIFICAÇÃO DE HIERARQUIA
    
    // 1. Se está atribuindo a um usuário específico
    if ($request->user_id) {
        $targetUser = User::findOrFail($request->user_id);
        
        // Gestor setorial só pode atribuir a usuários que ele gerencia
        if (!$currentUser->canManage($targetUser)) {
            abort(403, 'Você não pode atribuir a este usuário.');
        }
    }
    
    // 2. Se está criando para outra secretaria
    if ($currentUser->isSectorManager()) {
        if ($request->secretariat_id != $currentUser->secretariat_id) {
            abort(403, 'Você só pode criar registros na sua própria secretaria.');
        }
    }

    // Criar o registro
    $registro = SeuModelo::create($validated);

    return redirect()
        ->route('seu-modelo.index')
        ->with('success', 'Registro criado com sucesso!');
}
```

---

### PASSO 5: Método EDIT (Formulário de Edição)

**Objetivo:** Verificar se pode editar ESTE registro específico.

```php
public function edit(SeuModelo $registro)
{
    $currentUser = auth()->user();
    
    // VERIFICAR PERMISSÃO PARA EDITAR ESTE REGISTRO
    
    // Se o registro pertence a um usuário
    if ($registro->user_id) {
        $owner = User::findOrFail($registro->user_id);
        
        // Verificar se pode gerenciar o dono do registro
        if (!$currentUser->canManage($owner)) {
            abort(403, 'Você não tem permissão para editar este registro.');
        }
    }
    
    // Se o registro pertence a uma secretaria
    if ($registro->secretariat_id) {
        if ($currentUser->isSectorManager() && 
            $registro->secretariat_id != $currentUser->secretariat_id) {
            abort(403, 'Você não pode editar registros de outra secretaria.');
        }
    }

    // Buscar opções para o formulário (mesma lógica do create)
    if ($currentUser->isGeneralManager()) {
        $usuarios = User::all();
        $secretarias = Secretariat::all();
    } else {
        $usuarios = User::where('secretariat_id', $currentUser->secretariat_id)->get();
        $secretarias = Secretariat::where('id', $currentUser->secretariat_id)->get();
    }

    return view('seu-modelo.edit', compact('registro', 'usuarios', 'secretarias'));
}
```

---

### PASSO 6: Método UPDATE (Salvar Edição)

**Objetivo:** Validar permissões antes de atualizar.

```php
public function update(Request $request, SeuModelo $registro)
{
    $currentUser = auth()->user();
    
    // VERIFICAR PERMISSÃO (mesma lógica do edit)
    if ($registro->user_id) {
        $owner = User::findOrFail($registro->user_id);
        if (!$currentUser->canManage($owner)) {
            abort(403, 'Você não tem permissão para editar este registro.');
        }
    }
    
    if ($registro->secretariat_id) {
        if ($currentUser->isSectorManager() && 
            $registro->secretariat_id != $currentUser->secretariat_id) {
            abort(403, 'Você não pode editar registros de outra secretaria.');
        }
    }

    // Validação
    $validated = $request->validate([
        'nome' => 'required|string|max:255',
        'user_id' => 'required|exists:users,id',
        'secretariat_id' => 'required|exists:secretariats,id',
    ]);

    // VERIFICAR SE ESTÁ MUDANDO PARA OUTRO USUÁRIO/SECRETARIA
    if ($request->user_id != $registro->user_id) {
        $newOwner = User::findOrFail($request->user_id);
        if (!$currentUser->canManage($newOwner)) {
            abort(403, 'Você não pode transferir para este usuário.');
        }
    }
    
    if ($currentUser->isSectorManager()) {
        if ($request->secretariat_id != $currentUser->secretariat_id) {
            abort(403, 'Você não pode transferir para outra secretaria.');
        }
    }

    // Atualizar
    $registro->update($validated);

    return redirect()
        ->route('seu-modelo.index')
        ->with('success', 'Registro atualizado com sucesso!');
}
```

---

### PASSO 7: Método DESTROY (Excluir)

**Objetivo:** Verificar permissão para excluir.

```php
public function destroy(SeuModelo $registro)
{
    $currentUser = auth()->user();
    
    // VERIFICAR PERMISSÃO (mesma lógica do edit)
    if ($registro->user_id) {
        $owner = User::findOrFail($registro->user_id);
        if (!$currentUser->canManage($owner)) {
            abort(403, 'Você não tem permissão para excluir este registro.');
        }
    }
    
    if ($registro->secretariat_id) {
        if ($currentUser->isSectorManager() && 
            $registro->secretariat_id != $currentUser->secretariat_id) {
            abort(403, 'Você não pode excluir registros de outra secretaria.');
        }
    }

    // Excluir
    $registro->delete();

    return redirect()
        ->route('seu-modelo.index')
        ->with('success', 'Registro excluído com sucesso!');
}
```

---

## 🎨 PROTEGER VIEWS (Blade Templates)

### No Index (Lista)

```blade
{{-- Apenas gestores veem o botão de criar --}}
@if(auth()->user()->isManager())
    <a href="{{ route('seu-modelo.create') }}" class="btn btn-primary">
        <x-icon name="plus" /> Criar Novo
    </a>
@endif

{{-- Tabela de registros --}}
@foreach($registros as $registro)
    <tr>
        <td>{{ $registro->nome }}</td>
        <td>
            {{-- Verificar se pode editar ESTE registro --}}
            @if(auth()->user()->canManage($registro->user))
                <a href="{{ route('seu-modelo.edit', $registro) }}">
                    <x-icon name="edit" /> Editar
                </a>
                
                <form action="{{ route('seu-modelo.destroy', $registro) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">
                        <x-icon name="trash" /> Excluir
                    </button>
                </form>
            @else
                <span class="text-gray-400">Sem permissão</span>
            @endif
        </td>
    </tr>
@endforeach
```

### No Formulário (Create/Edit)

```blade
{{-- Mostrar campo apenas para gestor geral --}}
@if(auth()->user()->isGeneralManager())
    <div class="form-group">
        <label>Secretaria</label>
        <select name="secretariat_id">
            @foreach($secretarias as $secretaria)
                <option value="{{ $secretaria->id }}">{{ $secretaria->name }}</option>
            @endforeach
        </select>
    </div>
@else
    {{-- Gestor setorial: campo oculto com sua secretaria --}}
    <input type="hidden" name="secretariat_id" value="{{ auth()->user()->secretariat_id }}">
@endif
```

---

## 🔐 PROTEGER ROTAS (web.php)

### Opção 1: Middleware nas Rotas

```php
use App\Http\Middleware\CheckRoleHierarchy;

// Apenas gestores gerais
Route::middleware(['auth', 'role:general_manager'])->group(function () {
    Route::resource('configuracoes', ConfiguracoesController::class);
});

// Gestores gerais ou setoriais
Route::middleware(['auth', 'role:general_manager,sector_manager'])->group(function () {
    Route::resource('seu-modelo', SeuModeloController::class);
});

// Motoristas
Route::middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/minhas-corridas', [RunController::class, 'myRuns']);
});

// Todos autenticados (qualquer role)
Route::middleware(['auth'])->group(function () {
    Route::get('/perfil', [ProfileController::class, 'show']);
});
```

### Opção 2: Verificação no Controller (Mais Flexível)

```php
// Já fizemos isso nos métodos acima - é a forma recomendada!
// Porque permite lógicas mais complexas por método
```

---

## 📊 EXEMPLOS PRÁTICOS POR TIPO DE RECURSO

### Exemplo 1: Veículos (VehicleController)

```php
public function index()
{
    $user = auth()->user();
    
    // Gestor geral vê todos os veículos
    if ($user->isGeneralManager()) {
        $vehicles = Vehicle::with('secretariat')->latest()->paginate(15);
    }
    // Gestor setorial vê veículos da sua secretaria
    elseif ($user->isSectorManager()) {
        $vehicles = Vehicle::with('secretariat')
            ->where('secretariat_id', $user->secretariat_id)
            ->latest()
            ->paginate(15);
    }
    // Motorista vê apenas veículos que já usou
    else {
        $vehicles = Vehicle::with('secretariat')
            ->whereHas('runs', function($q) use ($user) {
                $q->where('driver_id', $user->id);
            })
            ->latest()
            ->paginate(15);
    }
    
    return view('vehicles.index', compact('vehicles'));
}

public function create()
{
    // Apenas gestores podem criar veículos
    if (!auth()->user()->isManager()) {
        abort(403, 'Apenas gestores podem cadastrar veículos.');
    }
    
    // ... resto do código
}
```

### Exemplo 2: Ordens de Serviço (ServiceOrderController)

```php
public function index()
{
    $user = auth()->user();
    
    if ($user->isGeneralManager()) {
        $orders = ServiceOrder::with(['vehicle', 'mechanic'])->latest()->paginate(15);
    }
    elseif ($user->isSectorManager()) {
        $orders = ServiceOrder::with(['vehicle', 'mechanic'])
            ->whereHas('vehicle', function($q) use ($user) {
                $q->where('secretariat_id', $user->secretariat_id);
            })
            ->latest()
            ->paginate(15);
    }
    elseif ($user->isMechanic()) {
        // Mecânico vê apenas suas ordens
        $orders = ServiceOrder::with(['vehicle', 'mechanic'])
            ->where('mechanic_id', $user->id)
            ->latest()
            ->paginate(15);
    }
    else {
        abort(403, 'Você não tem permissão para visualizar ordens de serviço.');
    }
    
    return view('service-orders.index', compact('orders'));
}
```

### Exemplo 3: Multas (FineController)

```php
public function update(Request $request, Fine $fine)
{
    $user = auth()->user();
    
    // Verificar se o usuário pode gerenciar o motorista da multa
    $driver = User::findOrFail($fine->driver_id);
    
    if (!$user->canManage($driver)) {
        abort(403, 'Você não pode editar multas de outros setores.');
    }
    
    // ... resto do código
}
```

---

## ⚠️ ERROS COMUNS E SOLUÇÕES

### ❌ ERRO 1: Usar role_id diretamente
```php
// ERRADO
if (auth()->user()->role_id == 1) { ... }

// CORRETO
if (auth()->user()->isGeneralManager()) { ... }
```

### ❌ ERRO 2: Não verificar hierarquia antes de editar
```php
// ERRADO
public function edit($id) {
    $registro = Model::findOrFail($id);
    return view('edit', compact('registro'));
}

// CORRETO
public function edit($id) {
    $registro = Model::findOrFail($id);
    if (!auth()->user()->canManage($registro->user)) {
        abort(403);
    }
    return view('edit', compact('registro'));
}
```

### ❌ ERRO 3: Proteger apenas no backend ou apenas no frontend
```php
// ❌ Apenas esconder botão não é suficiente!
@if(auth()->user()->isManager())
    <button>Excluir</button>
@endif

// ✅ Sempre proteger também no controller!
public function destroy($id) {
    if (!auth()->user()->isManager()) {
        abort(403);
    }
    // ... código
}
```

---

## 🚀 CHECKLIST RÁPIDO

Ao criar/editar um controller, verifique:

- [ ] Método `index()`: Filtra registros por hierarquia?
- [ ] Método `create()`: Verifica se pode criar?
- [ ] Método `store()`: Valida hierarquia antes de salvar?
- [ ] Método `edit()`: Verifica se pode editar ESTE registro?
- [ ] Método `update()`: Valida hierarquia antes de atualizar?
- [ ] Método `destroy()`: Verifica se pode excluir?
- [ ] Views: Botões aparecem apenas para quem tem permissão?
- [ ] Formulários: Campos limitados conforme hierarquia?

---

## 📚 RESUMO DOS MÉTODOS

```php
// VERIFICAR ROLE
$user->isGeneralManager()  // Gestor geral?
$user->isSectorManager()   // Gestor setorial?
$user->isManager()         // Qualquer gestor?
$user->isDriver()          // Motorista?
$user->isMechanic()        // Mecânico?

// VERIFICAR HIERARQUIA
$user->canManage($otherUser)  // Pode gerenciar outro usuário?
$user->hasHigherHierarchyThan($otherUser)  // Hierarquia maior?

// NO CONTROLLER
auth()->user()  // Usuário logado

// NA VIEW
@if(auth()->user()->isManager())
    ...
@endif
```

---

Seguindo este guia, você consegue implementar controle de hierarquia em QUALQUER controller do sistema! 🎯
de hirearquia
