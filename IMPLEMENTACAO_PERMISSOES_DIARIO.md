# Implementações Realizadas - Sistema de Permissões de Diário de Bordo

## Resumo das Alterações

### 1. Campo `secretariat_id` no Formulário de Veículos ✅ **CORRIGIDO**

**Arquivos Modificados:**
- `resources/views/vehicles/_form.blade.php` - Adicionado campo de seleção de secretaria
- `app/Http/Controllers/VehicleController.php` - Atualizado para incluir Secretariat e validação

**Funcionalidade:**
- Agora é possível selecionar a secretaria ao criar ou editar um veículo
- O campo mostra todas as secretarias disponíveis
- Validação implementada para garantir que a secretaria existe
- **BUG CORRIGIDO**: A secretaria agora é atualizada corretamente ao editar um veículo

**Problema Identificado e Corrigido:**
O método `update` estava sobrescrevendo o `secretariat_id` com o valor antigo, impedindo a atualização. A linha problemática foi removida.

---

### 2. Sistema de Permissões de Diário de Bordo ✅ **PADRONIZADO**

#### 2.1 Tabelas Criadas
**Migration:** `2025_10_08_140000_create_logbook_permissions_table.php`

**Tabelas:**
1. `logbook_permissions` - Armazena as permissões de acesso
   - `scope`: 'all' (todas secretarias), 'secretariat' (secretaria específica), 'vehicles' (veículos específicos)
   - `secretariat_id`: Referência à secretaria (quando scope = 'secretariat')
   - `is_active`: Status da permissão

2. `logbook_permission_vehicles` - Tabela pivot para veículos específicos
   - Relaciona permissões com veículos quando scope = 'vehicles'

#### 2.2 Models Criados
**Arquivo:** `app/Models/LogbookPermission.php`

**Funcionalidades:**
- `canAccessVehicle(User $user, Vehicle $vehicle)`: Verifica se um usuário pode acessar um veículo específico
- `getAccessibleVehicles(User $user)`: Retorna todos os veículos que um usuário pode acessar
- Relacionamentos com User, Secretariat e Vehicle

#### 2.3 Controller
**Arquivo:** `app/Http/Controllers/LogbookPermissionController.php`

**Rotas Implementadas:**
- `GET /logbook-permissions` - Lista todas as permissões
- `GET /logbook-permissions/create` - Formulário de criação
- `POST /logbook-permissions` - Criar nova permissão
- `GET /logbook-permissions/{id}/edit` - Formulário de edição
- `PUT /logbook-permissions/{id}` - Atualizar permissão
- `DELETE /logbook-permissions/{id}` - Excluir permissão

**Restrição:** Apenas gestores gerais (general_manager) podem acessar

#### 2.4 Views Criadas **✅ REFORMULADAS COM COMPONENTES PADRONIZADOS**
- `resources/views/logbook-permissions/index.blade.php` - Lista de permissões
- `resources/views/logbook-permissions/create.blade.php` - Criar permissão (REFORMULADO)
- `resources/views/logbook-permissions/edit.blade.php` - Editar permissão (REFORMULADO)

**Melhorias Implementadas:**
- Uso de componentes reutilizáveis do sistema (`x-input-label`, `x-ui.select`, `x-primary-button`, etc.)
- Integração com o novo componente `x-user-search` para pesquisa inteligente
- Layout e espaçamento consistente com o formulário de veículos
- Melhor organização visual com grid responsivo

---

### 3. **NOVO:** Componente Reutilizável de Pesquisa de Usuários ✅

**Arquivo Criado:** `resources/views/components/user-search.blade.php`

**Funcionalidades:**
- **Pesquisa inteligente** por nome ou CPF do usuário
- Resultados aparecem em dropdown abaixo do input
- **Filtro por roles** (opcional): pode filtrar apenas motoristas, gestores, etc.
- **Feedback visual**: mostra nome, CPF, role, secretaria e status de cada usuário
- **Loading indicator**: animação de carregamento durante a pesquisa
- **Debounce**: espera 300ms após digitar para fazer a busca (otimização)
- Mínimo de 2 caracteres para iniciar a pesquisa
- Componente totalmente reutilizável em qualquer formulário do sistema

**Propriedades do Componente:**
```blade
<x-user-search 
    name="user_id"                    <!-- Nome do campo -->
    label="Usuário *"                 <!-- Label do campo -->
    :roles="['driver', 'sector_manager']"  <!-- Filtro opcional de roles -->
    placeholder="Digite..."           <!-- Placeholder customizável -->
    :selectedId="$userId"            <!-- ID pré-selecionado (edição) -->
    :selectedName="$userName"        <!-- Nome pré-selecionado (edição) -->
    :required="true"                 <!-- Se é obrigatório -->
/>
```

**Exemplo de Uso:**
```blade
<!-- Pesquisar apenas motoristas -->
<x-user-search 
    name="driver_id"
    label="Motorista *"
    :roles="['driver']"
/>

<!-- Pesquisar qualquer usuário -->
<x-user-search 
    name="user_id"
    label="Selecione o Usuário"
    :required="false"
/>
```

#### 3.1 API de Busca de Usuários
**Arquivo:** `app/Http/Controllers/UserController.php` - Método `search()`

**Endpoint:** `GET /api/users/search?q={termo}&roles={roles_json}`

**Funcionalidades:**
- Pesquisa por nome ou CPF
- Filtro opcional por roles (JSON array)
- Respeita hierarquia: gestores setoriais veem apenas sua secretaria
- Retorna até 20 resultados
- Ordenação alfabética por nome
- Resposta JSON com informações completas do usuário

**Resposta da API:**
```json
[
  {
    "id": "uuid",
    "name": "João Silva",
    "cpf": "123.456.789-00",
    "email": "joao@example.com",
    "role": "driver",
    "secretariat": "Secretaria de Obras",
    "status": "active"
  }
]
```

**Rota Adicionada:**
```php
Route::get('/api/users/search', [UserController::class, 'search'])->name('api.users.search');
```

---

### 4. Submenu "Privilégios" na Sidebar ✅

**Arquivo Modificado:** `resources/views/layouts/navigation-links.blade.php`

**Localização:** Dentro do menu "Diário de Bordo"

**Visibilidade:** Apenas para gestores gerais (general_manager)

**Funcionalidades:**
- Link para gerenciar privilégios de acesso ao diário de bordo
- Ícone shield para identificação visual
- Integrado tanto na sidebar expandida quanto colapsada
- Aparece no popup quando a sidebar está colapsada

---

### 5. Correção do Bug Visual da Sidebar ✅

**Problema:** Quando o usuário mudava de página, os menus da sidebar que estavam abertos fechavam e abriam automaticamente, causando animação visual indesejada.

**Solução Implementada:**
- Modificado o sistema de inicialização do estado dos menus
- Agora o estado é carregado do localStorage apenas se a página atual não pertencer àquele grupo
- Se a página pertence ao grupo, o menu permanece aberto sem animação
- Uso da função `init()` do Alpine.js para controlar o estado inicial

---

## Como Usar o Componente de Pesquisa de Usuários

### Exemplo Básico:
```blade
<x-user-search 
    name="user_id"
    label="Usuário *"
/>
```

### Exemplo com Filtro de Roles:
```blade
<x-user-search 
    name="driver_id"
    label="Selecione o Motorista *"
    :roles="['driver']"
    placeholder="Digite o nome ou CPF do motorista..."
/>
```

### Exemplo para Edição (com dados pré-preenchidos):
```blade
<x-user-search 
    name="user_id"
    label="Usuário *"
    :selectedId="$entity->user_id"
    :selectedName="$entity->user->name"
/>
```

### Reutilização em Outros Formulários:
O componente pode ser usado em QUALQUER formulário do sistema que precise selecionar um usuário:
- Atribuir responsáveis
- Criar atribuições
- Delegações de tarefas
- Permissões específicas
- Etc.

---

## Estrutura de Dados

### logbook_permissions
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | UUID | Identificador único |
| user_id | UUID | Referência ao usuário |
| scope | ENUM | Tipo: 'all', 'secretariat', 'vehicles' |
| secretariat_id | UUID (nullable) | Secretaria (se scope = 'secretariat') |
| description | TEXT (nullable) | Observações |
| is_active | BOOLEAN | Status da permissão |

### logbook_permission_vehicles
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | UUID | Identificador único |
| logbook_permission_id | UUID | Referência à permissão |
| vehicle_id | UUID | Referência ao veículo |

---

## Observações Técnicas

1. **Performance:** Índices criados nas colunas mais consultadas
2. **Segurança:** Validações server-side em todas as operações
3. **UX:** Interface responsiva e intuitiva com feedback visual em tempo real
4. **Cascade Delete:** Permissões são excluídas ao excluir usuário ou secretaria
5. **Unique Constraint:** Evita duplicatas na tabela pivot
6. **Debounce na API:** Evita requisições excessivas durante digitação
7. **Componentes Reutilizáveis:** Sistema modular e consistente

---

## Rotas Adicionadas

```php
// Permissões de Diário de Bordo (apenas gestores gerais)
Route::resource('logbook-permissions', LogbookPermissionController::class);

// API de busca de usuários
Route::get('/api/users/search', [UserController::class, 'search'])->name('api.users.search');
```

---

## Status: ✅ IMPLEMENTADO, CORRIGIDO E PADRONIZADO

Todas as funcionalidades solicitadas foram implementadas com sucesso:
- ✅ Campo secretariat_id no formulário de veículos **CORRIGIDO** (agora atualiza corretamente)
- ✅ Tabela de permissões de diário de bordo
- ✅ Sistema completo de CRUD para permissões
- ✅ Views **REFORMULADAS** com componentes padronizados
- ✅ **NOVO:** Componente reutilizável de pesquisa de usuários
- ✅ **NOVO:** API de busca de usuários por nome/CPF com filtros
- ✅ Submenu "Privilégios" na sidebar
- ✅ Bug visual da sidebar corrigido

## Próximos Passos Sugeridos

O componente `x-user-search` agora pode ser replicado em outros formulários do sistema:
- Formulário de criação de corridas (selecionar motorista)
- Atribuição de responsáveis em ordens de serviço
- Delegação de tarefas
- Qualquer outro lugar que precise selecionar usuários
