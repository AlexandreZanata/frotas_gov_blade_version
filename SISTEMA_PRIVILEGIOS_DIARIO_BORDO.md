# Sistema de Privilégios do Diário de Bordo - Implementação Completa

## Resumo das Mudanças

O sistema de privilégios do diário de bordo foi completamente ajustado para funcionar corretamente com múltiplas secretarias específicas. Agora os usuários podem ter privilégios para acessar:

1. **Todas as secretarias** (scope: 'all')
2. **Secretarias específicas** (scope: 'secretariat') - Pode escolher 2, 3 ou quantas secretarias desejar
3. **Veículos específicos** (scope: 'vehicles') - Pode escolher veículos individuais

## Arquivos Modificados

### 1. `app/Models/LogbookPermission.php`

**Método atualizado: `canAccessVehicle()`**
- Agora verifica corretamente as múltiplas secretarias através da tabela pivot `logbook_permission_secretariats`
- Utiliza o relacionamento `secretariats()` (plural) em vez de `secretariat_id` (campo legado)

**Novos métodos adicionados:**
- `getUserAccessibleSecretariatIds(User $user): array` - Retorna os IDs de todas as secretarias que o usuário pode acessar
- `getUserAccessibleVehicleIds(User $user): array` - Retorna os IDs de todos os veículos que o usuário pode acessar
- `userHasActivePermissions(User $user): bool` - Verifica se o usuário tem permissões ativas

### 2. `app/Services/LogbookService.php`

**Método atualizado: `getAvailableVehicles()`**
- Agora utiliza o sistema de privilégios do `LogbookPermission` em vez de apenas filtrar pela secretaria do usuário
- Verifica se o usuário tem permissões ativas antes de buscar veículos
- Retorna apenas os veículos que o usuário tem privilégio para acessar

### 3. `app/Models/User.php`

**Novos métodos adicionados:**
- `canAccessVehicle(Vehicle $vehicle): bool` - Verifica se o usuário pode acessar um veículo específico
- `getAccessibleVehicles(): Collection` - Retorna todos os veículos que o usuário pode acessar
- `getAccessibleSecretariats(): Collection` - Retorna todas as secretarias que o usuário pode acessar
- `hasLogbookPermissions(): bool` - Verifica se o usuário tem permissões ativas no diário de bordo

### 4. `app/Http/Controllers/RunController.php`

**Validações de permissão adicionadas aos métodos:**
- `getVehicleData()` - Valida permissão antes de retornar dados do veículo via AJAX
- `storeVehicle()` - Valida permissão antes de salvar a seleção do veículo
- `checklistForm()` - Valida permissão antes de exibir o formulário de checklist
- `storeChecklistAndCreateRun()` - Valida permissão antes de criar a corrida

Todas as validações incluem:
- Verificação de permissão usando `LogbookPermission::canAccessVehicle()`
- Mensagens de erro apropriadas
- Redirecionamentos de segurança

## Estrutura do Banco de Dados

### Tabela: `logbook_permissions`
```
- id (UUID)
- user_id (UUID) - Referência ao usuário
- scope (string) - 'all', 'secretariat' ou 'vehicles'
- secretariat_id (UUID, nullable) - Campo LEGADO, não mais usado
- description (text, nullable)
- is_active (boolean)
- created_at
- updated_at
```

### Tabela Pivot: `logbook_permission_secretariats`
```
- logbook_permission_id (UUID)
- secretariat_id (UUID)
- created_at
- updated_at
- PRIMARY KEY: (logbook_permission_id, secretariat_id)
```

### Tabela Pivot: `logbook_permission_vehicles`
```
- logbook_permission_id (UUID)
- vehicle_id (UUID)
- created_at
- updated_at
- PRIMARY KEY: (logbook_permission_id, vehicle_id)
```

## Fluxo de Funcionamento

### 1. Criação de Privilégio

**Admin Geral acessa:** `/logbook-permissions/create`

**Seleciona:**
- Usuário (motorista, gestor setorial ou gestor geral)
- Tipo de privilégio:
  - **Todas as secretarias**: Acesso total
  - **Secretarias específicas**: Seleciona 2, 3 ou mais secretarias
  - **Veículos específicos**: Seleciona veículos individuais

**Sistema salva:**
- Registro principal em `logbook_permissions`
- Se secretarias: associações em `logbook_permission_secretariats`
- Se veículos: associações em `logbook_permission_vehicles`

### 2. Acesso ao Diário de Bordo

**Motorista acessa:** `/logbook/start`

**Sistema verifica:**
1. Se o usuário tem permissões ativas (`userHasActivePermissions()`)
2. Busca todos os veículos acessíveis (`getUserAccessibleVehicleIds()`)
3. Exibe apenas os veículos permitidos

**Caso o usuário selecione um veículo:**
1. Valida se tem permissão para aquele veículo específico
2. Se não tiver, exibe erro e redireciona
3. Se tiver, permite continuar com o fluxo

### 3. Verificação de Permissões

**Lógica de verificação por escopo:**

```php
// Scope 'all' - Acesso total
if ($permission->scope === 'all') {
    return true;
}

// Scope 'secretariat' - Verifica tabela pivot
if ($permission->scope === 'secretariat') {
    $hasAccess = $permission->secretariats()
        ->where('secretariat_id', $vehicle->secretariat_id)
        ->exists();
    return $hasAccess;
}

// Scope 'vehicles' - Verifica veículos específicos
if ($permission->scope === 'vehicles') {
    return $permission->vehicles()
        ->where('vehicle_id', $vehicle->id)
        ->exists();
}
```

## Exemplos de Uso

### Exemplo 1: Motorista com acesso a 2 secretarias específicas

```php
$permission = LogbookPermission::create([
    'user_id' => $motorista->id,
    'scope' => 'secretariat',
    'is_active' => true,
]);

// Associar secretarias de Educação e Saúde
$permission->secretariats()->attach([
    $secretariaEducacao->id,
    $secretariaSaude->id,
]);

// Resultado: Motorista pode acessar todos os veículos dessas 2 secretarias
```

### Exemplo 2: Motorista com acesso a veículos específicos

```php
$permission = LogbookPermission::create([
    'user_id' => $motorista->id,
    'scope' => 'vehicles',
    'is_active' => true,
]);

// Associar 3 veículos específicos
$permission->vehicles()->attach([
    $veiculo1->id,
    $veiculo2->id,
    $veiculo3->id,
]);

// Resultado: Motorista pode acessar apenas esses 3 veículos
```

### Exemplo 3: Gestor com acesso total

```php
$permission = LogbookPermission::create([
    'user_id' => $gestor->id,
    'scope' => 'all',
    'is_active' => true,
]);

// Resultado: Gestor pode acessar todos os veículos de todas as secretarias
```

## Melhorias de Segurança

1. **Validação em múltiplas camadas:**
   - Model: `LogbookPermission::canAccessVehicle()`
   - Service: `LogbookService::getAvailableVehicles()`
   - Controller: Validações em cada método

2. **Proteção contra acesso indevido:**
   - Veículos não permitidos não aparecem na lista
   - Tentativas de acesso direto são bloqueadas
   - Mensagens de erro claras

3. **Limpeza de sessão:**
   - Limpa seleção de veículo ao detectar falta de permissão
   - Evita estados inconsistentes

## Como Testar

### Teste 1: Criar privilégio para múltiplas secretarias
1. Login como Admin Geral
2. Acessar `/logbook-permissions/create`
3. Selecionar um motorista
4. Escolher "Secretarias específicas"
5. Selecionar 2 ou 3 secretarias
6. Salvar
7. Login como o motorista
8. Verificar que apenas veículos das secretarias selecionadas aparecem

### Teste 2: Criar privilégio para veículos específicos
1. Login como Admin Geral
2. Acessar `/logbook-permissions/create`
3. Selecionar um motorista
4. Escolher "Veículos específicos"
5. Selecionar 2 ou 3 veículos
6. Salvar
7. Login como o motorista
8. Verificar que apenas os veículos selecionados aparecem

### Teste 3: Validar bloqueio de acesso
1. Criar privilégio para o motorista com apenas 1 secretaria
2. Login como o motorista
3. Tentar acessar diretamente um veículo de outra secretaria (via URL)
4. Verificar que é bloqueado com mensagem de erro

## Notas Importantes

- **Campo `secretariat_id` é LEGADO**: Não deve mais ser usado. Mantido apenas para compatibilidade.
- **Gestores Gerais**: Sempre têm acesso total, independente de privilégios configurados
- **Privilégios inativos**: Não são considerados nas verificações (is_active = false)
- **Performance**: As consultas usam eager loading para otimizar o desempenho

## Próximos Passos Sugeridos

1. Adicionar interface para usuários visualizarem seus próprios privilégios
2. Criar relatório de privilégios por secretaria
3. Adicionar notificações quando privilégios são alterados
4. Implementar log de tentativas de acesso negado
5. Criar dashboard para gestores visualizarem uso dos veículos por privilégio

