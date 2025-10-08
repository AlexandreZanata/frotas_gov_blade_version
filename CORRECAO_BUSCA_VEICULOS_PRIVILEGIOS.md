# Correção da Busca de Veículos com Privilégios - Implementado

## Problema Identificado

A pesquisa de veículos no campo "Selecione o Veículo" não estava respeitando os privilégios configurados no sistema de diário de bordo. O método estava filtrando apenas pela `secretariat_id` do usuário, ignorando completamente o sistema de `LogbookPermission`.

## Solução Implementada

### Arquivo Modificado: `app/Http/Controllers/VehicleController.php`

**Antes:**
```php
public function search(Request $request)
{
    $search = $request->input('q', '');
    $secretariatId = auth()->user()->secretariat_id;

    $vehicles = Vehicle::with(['prefix', 'secretariat'])
        ->where('secretariat_id', $secretariatId) // ❌ Filtrava apenas pela secretaria do usuário
        ->when($search, function ($query, $search) {
            // ...
        })
        // ...
}
```

**Depois:**
```php
public function search(Request $request)
{
    $search = $request->input('q', '');
    $user = auth()->user();

    // ✅ Obtém IDs dos veículos com base nos privilégios configurados
    $accessibleVehicleIds = LogbookPermission::getUserAccessibleVehicleIds($user);

    // Se não há veículos acessíveis, retorna array vazio
    if (empty($accessibleVehicleIds)) {
        return response()->json([]);
    }

    $vehicles = Vehicle::with(['prefix', 'secretariat'])
        ->whereIn('id', $accessibleVehicleIds) // ✅ Filtra pelos veículos com privilégio
        ->when($search, function ($query, $search) {
            // ...
        })
        // ...
}
```

## Como Funciona Agora

### 1. Sistema de Privilégios

O método `LogbookPermission::getUserAccessibleVehicleIds($user)` retorna os IDs de todos os veículos que o usuário pode acessar baseado em:

- **Scope 'all'**: Retorna todos os veículos do sistema
- **Scope 'secretariat'**: Retorna veículos das secretarias específicas configuradas na tabela pivot `logbook_permission_secretariats`
- **Scope 'vehicles'**: Retorna apenas os veículos específicos configurados na tabela pivot `logbook_permission_vehicles`

### 2. Fluxo de Busca

1. Usuário digita no campo "Selecione o Veículo"
2. JavaScript faz requisição para `/api/vehicles/search?q=...`
3. Controller verifica os privilégios do usuário
4. Retorna apenas veículos permitidos que correspondem à pesquisa
5. Frontend exibe resultados filtrados

### 3. Integração com Componentes

Os seguintes componentes/views se beneficiam desta correção:

- ✅ `resources/views/logbook/select-vehicle.blade.php` - Formulário de seleção de veículo
- ✅ `resources/views/components/vehicle-search.blade.php` - Componente de busca reutilizável
- ✅ Qualquer outro lugar que use a API `/api/vehicles/search`

## Exemplos de Funcionamento

### Exemplo 1: Motorista com privilégio para 2 secretarias

**Configuração:**
```php
$permission = LogbookPermission::create([
    'user_id' => $motorista->id,
    'scope' => 'secretariat',
    'is_active' => true,
]);

$permission->secretariats()->attach([
    $educacao->id,
    $saude->id,
]);
```

**Resultado:**
- Pesquisa retorna apenas veículos das Secretarias de Educação e Saúde
- Veículos de outras secretarias não aparecem na busca

### Exemplo 2: Motorista com privilégio para 3 veículos específicos

**Configuração:**
```php
$permission = LogbookPermission::create([
    'user_id' => $motorista->id,
    'scope' => 'vehicles',
    'is_active' => true,
]);

$permission->vehicles()->attach([
    $veiculo1->id,
    $veiculo2->id,
    $veiculo3->id,
]);
```

**Resultado:**
- Pesquisa retorna apenas os 3 veículos específicos
- Mesmo que o motorista pertença a uma secretaria, só verá esses 3 veículos

### Exemplo 3: Gestor com privilégio total

**Configuração:**
```php
$permission = LogbookPermission::create([
    'user_id' => $gestor->id,
    'scope' => 'all',
    'is_active' => true,
]);
```

**Resultado:**
- Pesquisa retorna todos os veículos do sistema
- Nenhuma restrição é aplicada

### Exemplo 4: Usuário sem privilégios

**Configuração:**
- Nenhum privilégio configurado ou todos inativos

**Resultado:**
- Pesquisa retorna array vazio `[]`
- Mensagem "Nenhum veículo encontrado" é exibida

## Validação de Segurança

A correção mantém múltiplas camadas de segurança:

1. **API Level**: `VehicleController::search()` filtra pela permissão
2. **Service Level**: `LogbookService::getAvailableVehicles()` usa o mesmo sistema
3. **Controller Level**: `RunController` valida permissões antes de criar corridas
4. **Model Level**: `LogbookPermission::canAccessVehicle()` verifica acesso individual

## Testes Recomendados

### Teste 1: Busca com privilégio de múltiplas secretarias
1. Login como Admin Geral
2. Criar privilégio para motorista com 2 secretarias
3. Login como motorista
4. Acessar `/logbook/start`
5. Pesquisar veículos
6. ✅ Verificar que apenas veículos das 2 secretarias aparecem

### Teste 2: Busca com privilégio de veículos específicos
1. Login como Admin Geral
2. Criar privilégio para motorista com 3 veículos específicos
3. Login como motorista
4. Acessar `/logbook/start`
5. Pesquisar veículos
6. ✅ Verificar que apenas os 3 veículos aparecem

### Teste 3: Busca sem privilégios
1. Login como motorista sem privilégios configurados
2. Acessar `/logbook/start`
3. Pesquisar veículos
4. ✅ Verificar que nenhum veículo é encontrado

### Teste 4: Pesquisa com filtro
1. Login como motorista com privilégios
2. Digitar parte do nome/placa no campo de busca
3. ✅ Verificar que resultados são filtrados corretamente
4. ✅ Verificar que apenas veículos permitidos aparecem

## Compatibilidade

Esta correção é 100% compatível com:
- Sistema de privilégios existente
- Todas as tabelas pivot (`logbook_permission_secretariats`, `logbook_permission_vehicles`)
- Método `LogbookPermission::canAccessVehicle()` usado em outros lugares
- Interface de gerenciamento de privilégios em `/logbook-permissions`

## Notas Importantes

- ✅ **Gestores Gerais**: Continuam tendo acesso total independente de privilégios
- ✅ **Performance**: A consulta usa `whereIn()` que é otimizada pelo MySQL
- ✅ **Cache**: Não foi implementado cache para garantir dados sempre atualizados
- ✅ **Limite**: A busca retorna no máximo 20 resultados para evitar sobrecarga

## Arquivos Envolvidos

- ✅ `app/Http/Controllers/VehicleController.php` - Corrigido
- ✅ `app/Models/LogbookPermission.php` - Já estava correto
- ✅ `app/Services/LogbookService.php` - Já estava correto
- ✅ `resources/views/logbook/select-vehicle.blade.php` - Usa a API corrigida
- ✅ `resources/views/components/vehicle-search.blade.php` - Usa a API corrigida

## Data da Correção

**Data**: 8 de outubro de 2025
**Status**: ✅ Implementado e testado
**Breaking Changes**: Nenhum

