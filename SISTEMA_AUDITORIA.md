# Sistema de Auditoria - Documentação

## Visão Geral

O sistema de auditoria registra automaticamente todas as operações de CRUD (Create, Read, Update, Delete) realizadas no sistema, mantendo um histórico completo de alterações.

## Estrutura da Tabela `audit_logs`

```sql
CREATE TABLE `audit_logs` (
  `id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` char(36) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
)
```

## Como Usar

### 1. Adicionar Auditoria a um Model

Para habilitar auditoria em um modelo, adicione a trait `Auditable`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class YourModel extends Model
{
    use Auditable;
    
    // ... resto do código
}
```

### 2. Modelos com Auditoria Habilitada

Os seguintes modelos já possuem auditoria automática:

- **User** - Rastreia criação, atualização e exclusão de usuários
- **Vehicle** - Rastreia operações em veículos
- **DefaultPassword** - Rastreia gerenciamento de senhas padrão

### 3. O que é Registrado

#### Criação (`created`)
Registra todos os dados do novo registro criado.

#### Atualização (`updated`)
Registra:
- Valores anteriores (antes da alteração)
- Novos valores (após a alteração)
- Apenas os campos que foram alterados

#### Exclusão (`deleted`)
Registra todos os dados do registro antes de ser excluído.

### 4. Campos Sensíveis

Campos sensíveis são automaticamente removidos dos logs:
- `password`
- `remember_token`

## Exemplos de Uso

### Exemplo 1: Criar Usuário
```php
$user = User::create([
    'name' => 'João Silva',
    'email' => 'joao@example.com',
    'cpf' => '123.456.789-00',
    // ... outros campos
]);

// Log criado automaticamente:
// Action: created
// New Values: { name: "João Silva", email: "joao@example.com", ... }
```

### Exemplo 2: Atualizar Veículo
```php
$vehicle->update([
    'plate' => 'ABC-1234',
    'status_id' => '...',
]);

// Log criado automaticamente:
// Action: updated
// Old Values: { plate: "DEF-5678", status_id: "..." }
// New Values: { plate: "ABC-1234", status_id: "..." }
```

### Exemplo 3: Excluir Senha Padrão
```php
$defaultPassword->delete();

// Log criado automaticamente:
// Action: deleted
// Old Values: { name: "senha_motorista", description: "...", ... }
```

## Visualização de Logs

### Permissões

Apenas **Gestores Gerais** podem visualizar logs de auditoria.

### Acessando os Logs

1. Acesse: `/audit-logs`
2. Use os filtros disponíveis:
   - **Pesquisa**: Busca por descrição, ID ou nome de usuário
   - **Ação**: Filtra por tipo (Criação, Atualização, Exclusão)
   - **Tipo**: Filtra por modelo (User, Vehicle, etc.)

### Detalhes do Log

Clique em "Ver Detalhes" para visualizar:
- Informações gerais (data, usuário, ação)
- Valores anteriores (JSON formatado)
- Novos valores (JSON formatado)
- Comparação lado a lado das alterações

## Personalização

### Customizar Descrição

Você pode customizar a descrição dos logs sobrescrevendo o método `getAuditDescription`:

```php
protected function getAuditDescription(string $action): string
{
    return match($action) {
        'created' => "Novo veículo {$this->plate} cadastrado",
        'updated' => "Veículo {$this->plate} foi modificado",
        'deleted' => "Veículo {$this->plate} foi removido",
        default => parent::getAuditDescription($action),
    };
}
```

### Excluir Campos do Log

Para excluir campos específicos do log, sobrescreva `getAuditableAttributes`:

```php
protected function getAuditableAttributes(): array
{
    $attributes = $this->getAttributes();
    
    // Remove campos que não devem ser logados
    unset($attributes['temporary_field']);
    
    return $attributes;
}
```

## Boas Práticas

1. **Retenção**: Configure políticas de retenção para limpar logs antigos
2. **Backup**: Faça backup regular da tabela `audit_logs`
3. **Monitoramento**: Configure alertas para ações suspeitas
4. **Privacidade**: Nunca logue dados sensíveis (senhas, tokens, etc.)

## Queries Úteis

### Logs de um usuário específico
```php
$logs = AuditLog::where('user_id', $userId)
    ->latest()
    ->get();
```

### Logs de alterações em um registro específico
```php
$logs = AuditLog::where('auditable_type', Vehicle::class)
    ->where('auditable_id', $vehicleId)
    ->latest()
    ->get();
```

### Logs das últimas 24 horas
```php
$logs = AuditLog::where('created_at', '>=', now()->subDay())
    ->latest()
    ->get();
```

## Troubleshooting

### Logs não estão sendo criados

Verifique se:
1. A trait `Auditable` está adicionada ao modelo
2. Os eventos do Eloquent estão sendo disparados (ex: `Model::create()` dispara, mas `DB::insert()` não)
3. O usuário está autenticado (para registrar o `user_id`)

### Performance

Se houver muitos logs:
1. Adicione índices na tabela:
   ```sql
   CREATE INDEX idx_auditable ON audit_logs(auditable_type, auditable_id);
   CREATE INDEX idx_created_at ON audit_logs(created_at);
   CREATE INDEX idx_user_id ON audit_logs(user_id);
   ```

2. Implemente paginação nas queries
3. Configure arquivamento de logs antigos

## Segurança

- Logs são **imutáveis** - não podem ser editados após criação
- Apenas leitura para gestores gerais
- IPs e User Agents são registrados para rastreamento
- Campos sensíveis são automaticamente removidos

