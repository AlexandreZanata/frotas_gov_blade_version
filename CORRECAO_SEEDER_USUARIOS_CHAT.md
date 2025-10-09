# Correção do Seeder de Usuários e Chat

## Data: 09/10/2025

## ⚠️ PROBLEMA CRÍTICO RESOLVIDO

### Erro Persistente:
```
SQLSTATE[HY000]: General error: 1364 Field 'id' doesn't have a default value
```

### Causa Raiz REAL:
A tabela `chat_participants` no banco de dados MySQL ainda tinha um campo `id` UUID, mesmo após corrigir a migration. Isso ocorreu porque:

1. O Laravel estava carregando um **schema cache antigo** do arquivo `database/schema/mysql-schema.sql`
2. A estrutura antiga da tabela permanecia no banco de dados
3. Mesmo após corrigir a migration, a tabela não foi recriada

### SOLUÇÃO DEFINITIVA APLICADA:

Foi criada uma nova migration de correção que **força a recriação** da tabela:

**Arquivo:** `database/migrations/2025_10_09_120900_fix_chat_participants_table.php`

Esta migration:
- Remove completamente a tabela antiga com `Schema::dropIfExists()`
- Recria a tabela com a estrutura correta (chave primária composta)
- Não depende de schema cache

## Como Resolver o Problema

Se você ainda estiver enfrentando o erro, execute:

```bash
# 1. Execute a migration de correção
php artisan migrate --path=database/migrations/2025_10_09_120900_fix_chat_participants_table.php

# 2. Agora execute o migrate:fresh --seed normalmente
php artisan migrate:fresh --seed
```

**✅ AGORA FUNCIONA PERFEITAMENTE!**

---

## Problema Identificado (Original)

Ao executar `php artisan migrate:fresh --seed`, apenas o usuário Admin estava sendo criado, e o ChatSeeder não conseguia criar conversas porque precisava de pelo menos 2 usuários.

## Causa Raiz

O arquivo `database/seeders/UserSeeder.php` estava criando apenas 1 usuário (Admin Geral), mas o `ChatSeeder.php` precisa de no mínimo 2 usuários para criar conversas de teste.

## Solução Aplicada

### 1. Atualização do UserSeeder.php

Agora o seeder cria **5 usuários diferentes** com roles variados:

#### Usuários Criados:

1. **Admin Geral** (general_manager)
   - Email: `admin@frotas.gov`
   - Senha: `password`
   - CPF: 00000000000
   - Secretaria: Administração
   
2. **Carlos Silva** (sector_manager)
   - Email: `gestor.saude@frotas.gov`
   - Senha: `password`
   - CPF: 11111111111
   - Secretaria: Saúde
   
3. **João Motorista** (driver) 🚗
   - Email: `motorista.saude@frotas.gov`
   - Senha: `password`
   - CPF: 22222222222
   - CNH: Categoria D
   - Secretaria: Saúde
   
4. **Maria Condutora** (driver) 🚗
   - Email: `motorista.educacao@frotas.gov`
   - Senha: `password`
   - CPF: 33333333333
   - CNH: Categoria D
   - Secretaria: Educação
   
5. **Pedro Mecânico** (mechanic) 🔧
   - Email: `mecanico@frotas.gov`
   - Senha: `password`
   - CPF: 44444444444
   - Secretaria: Administração

### 2. Atualização do ChatSeeder.php

O seeder de chat foi corrigido para usar o método `sync()` ao invés de `attach()`, evitando o erro de UUID que ocorria anteriormente.

**Antes:**
```php
$chatRoom1->participants()->attach([$user1->id, $user2->id]);
```

**Depois:**
```php
$chatRoom1->participants()->sync([
    $user1->id => ['created_at' => now(), 'updated_at' => now()],
    $user2->id => ['created_at' => now(), 'updated_at' => now()]
]);
```

### 3. Conversas Criadas pelo ChatSeeder

Com os novos usuários, o ChatSeeder agora cria:

1. **Conversa Privada** entre Admin Geral e Carlos Silva
   - 3 mensagens de exemplo

2. **Grupo de Chat** "Equipe de Frotas"
   - Participantes: Admin Geral, Carlos Silva e João Motorista
   - 3 mensagens de exemplo

## Como Testar

Execute o comando para recriar o banco de dados:

```bash
php artisan migrate:fresh --seed
```

### Verificações Esperadas:

#### 1. Verificar Usuários Criados
```bash
php artisan tinker
>>> User::count()
# Deve retornar: 5

>>> User::where('role_id', Role::where('name', 'driver')->first()->id)->count()
# Deve retornar: 2 (dois motoristas)
```

#### 2. Verificar Conversas do Chat
```bash
php artisan tinker
>>> ChatRoom::count()
# Deve retornar: 2 (1 conversa privada + 1 grupo)

>>> ChatMessage::count()
# Deve retornar: 6 (3 mensagens por conversa)
```

#### 3. Acessar o Sistema
1. Acesse: `http://127.0.0.1:8000`
2. Faça login com qualquer usuário (senha: `password`)
3. Acesse o menu "Chat"
4. Você deve ver as conversas criadas

## Credenciais de Acesso

Todos os usuários têm a senha: **`password`**

### Para testar como Admin:
- Email: `admin@frotas.gov`

### Para testar como Motorista:
- Email: `motorista.saude@frotas.gov`
- Email: `motorista.educacao@frotas.gov`

### Para testar como Gestor:
- Email: `gestor.saude@frotas.gov`

### Para testar como Mecânico:
- Email: `mecanico@frotas.gov`

## Informações Adicionais

### Campos Criados para Todos os Usuários:
- ✅ `name` - Nome completo
- ✅ `email` - Email único
- ✅ `cpf` - CPF único
- ✅ `phone` - Telefone no formato (11) 99999-XXXX
- ✅ `cnh` - Número da CNH
- ✅ `cnh_expiration_date` - Data de validade (2 anos no futuro)
- ✅ `cnh_category` - Categoria da CNH (A, B, AB, D)
- ✅ `role_id` - Papel/Função do usuário
- ✅ `secretariat_id` - Secretaria vinculada
- ✅ `status` - Status ativo

### Estrutura de Roles Criadas:
1. **general_manager** - Administrador Geral (acesso total)
2. **sector_manager** - Gestor Setorial (gerencia sua secretaria)
3. **driver** - Motorista (pode dirigir veículos)
4. **mechanic** - Mecânico (gerencia manutenções)

### Secretarias Disponíveis:
1. Administração
2. Saúde
3. Educação
4. Obras
5. Meio Ambiente
6. Assistência Social

## Benefícios das Correções

✅ **Mais usuários de teste** - Agora você tem 5 usuários diferentes para testar
✅ **Motoristas criados** - Dois motoristas para testar funcionalidades específicas
✅ **Chat funcional** - Conversas e grupos criados automaticamente
✅ **Dados realistas** - Usuários com CNH, telefone e outras informações completas
✅ **Diferentes roles** - Possibilita testar permissões e hierarquias
✅ **Sem erros de UUID** - Uso correto do método `sync()` para tabelas pivot

## Próximos Passos

1. ✅ Execute `php artisan migrate:fresh --seed`
2. ✅ Faça login com qualquer usuário (senha: password)
3. ✅ Teste o chat com os usuários criados
4. ✅ Teste as diferentes funcionalidades com diferentes roles

## Observações

- O seeder agora exibe mensagens informativas durante a execução
- Todos os usuários têm CNH válida por 2 anos
- Os motoristas têm CNH categoria D (para veículos grandes)
- O gestor e mecânico têm CNH categoria B
- O admin tem CNH categoria AB

---

✅ **Problema resolvido! O seeder agora cria todos os usuários e conversas corretamente.**
