# Módulo de Troca de Pneus - Documentação Completa

## Visão Geral

O Módulo de Troca de Pneus é um sistema completo para gerenciamento visual e interativo de pneus da frota, permitindo controle total sobre instalação, rodízio, manutenção e histórico de cada pneu.

## Estrutura do Banco de Dados

### Tabelas Principais

#### 1. `tires` - Pneus Físicos
- **id**: UUID único
- **inventory_item_id**: Ligação com o tipo de pneu no estoque
- **brand**: Marca do pneu (ex: Michelin, Pirelli)
- **model**: Modelo/medida (ex: 175/70 R13)
- **serial_number**: Número de série único (número de fogo)
- **dot_number**: Código DOT (opcional)
- **purchase_date**: Data de compra
- **purchase_price**: Valor de compra
- **lifespan_km**: Vida útil estimada em KM
- **current_km**: KM rodados pelo pneu
- **status**: Em Estoque | Em Uso | Em Manutenção | Recapagem | Descartado
- **condition**: Novo | Bom | Atenção | Crítico
- **current_vehicle_id**: Veículo atual (null se em estoque)
- **current_position**: Posição no veículo (1, 2, 3, etc)
- **notes**: Observações

#### 2. `tire_events` - Histórico de Eventos
- **id**: UUID único
- **tire_id**: Pneu relacionado
- **user_id**: Usuário que realizou a ação
- **vehicle_id**: Veículo envolvido
- **event_type**: Cadastro | Instalação | Rodízio | Troca | Manutenção | Recapagem | Descarte
- **description**: Descrição do evento
- **km_at_event**: KM do veículo no momento
- **event_date**: Data/hora do evento

#### 3. `vehicle_tire_layouts` - Layouts de Veículos
- **id**: ID sequencial
- **name**: Nome do layout (ex: "Carro (4 Pneus)")
- **layout_data**: JSON com posições e coordenadas para o diagrama interativo

## Funcionalidades Principais

### 1. Dashboard Principal (`/tires`)
**Arquivo**: `resources/views/tires/index.blade.php`

Exibe:
- ✅ Estatísticas vitais (Críticos, Atenção, Vida Útil Média, Total de Veículos)
- ✅ Status dos pneus (Em Uso, Em Estoque, Manutenção)
- ✅ Alertas de pneus críticos e em atenção
- ✅ Ação rápida para pneus que exigem atenção

### 2. Gestão Visual de Veículos (`/tires/vehicles`)
**Arquivo**: `resources/views/tires/vehicles.blade.php`

- ✅ Lista todos os veículos com pneus instalados
- ✅ Visualização rápida da condição dos pneus por veículo
- ✅ Alertas visuais para veículos com pneus críticos
- ✅ Busca por nome ou placa do veículo

### 3. Diagrama Interativo de Pneus (`/tires/vehicles/{id}`)
**Arquivo**: `resources/views/tires/vehicle-detail.blade.php`

**Funcionalidades**:
- ✅ Diagrama visual interativo baseado no tipo de veículo
- ✅ Cada pneu é clicável e mostra:
  - Condição (cores: Verde=Novo, Azul=Bom, Amarelo=Atenção, Vermelho=Crítico)
  - Percentual de uso
  - Marca e modelo
- ✅ Modal de ações ao clicar em um pneu:
  - **Rodízio**: Trocar posição com outro pneu do mesmo veículo
  - **Trocar Pneu**: Substituir por um do estoque
  - **Remover**: Enviar para estoque/manutenção/recapagem
  - **Registrar Evento**: Cadastrar evento especial
  - **Ver Histórico**: Histórico completo do pneu

### 4. Estoque de Pneus (`/tires/stock`)
**Arquivo**: `resources/views/tires/stock.blade.php`

- ✅ Lista todos os pneus em estoque
- ✅ Filtros por condição (Novo, Bom, Atenção, Crítico)
- ✅ Busca por marca, modelo ou número de série
- ✅ Visualização da vida útil com barra de progresso
- ✅ Acesso rápido ao histórico

### 5. Cadastro de Pneus (`/tires/create`)
**Arquivo**: `resources/views/tires/create.blade.php`

Campos:
- ✅ Tipo de pneu (do inventário)
- ✅ Marca e modelo
- ✅ Número de série (único)
- ✅ Código DOT (opcional)
- ✅ Data e valor de compra
- ✅ Vida útil estimada em KM
- ✅ Observações

### 6. Histórico do Pneu (`/tires/history/{id}`)
**Arquivo**: `resources/views/tires/history.blade.php`

- ✅ Informações detalhadas do pneu
- ✅ Linha do tempo visual de todos os eventos
- ✅ Cada evento mostra:
  - Tipo de evento com ícone colorido
  - Data/hora e usuário responsável
  - Descrição completa
  - Veículo envolvido (se aplicável)
  - KM no momento do evento

## Lógica de Negócio

### Service: `TireService`
**Arquivo**: `app/Services/TireService.php`

#### Métodos Principais:

1. **`getDashboardStats()`**
   - Retorna estatísticas para o dashboard
   - Calcula vida útil média da frota

2. **`getVehicleLayout($vehicle)`**
   - Retorna o layout de pneus apropriado baseado na categoria do veículo
   - Mapeia: Carro→4 pneus, Van→6, Caminhão→10, Ônibus→6, Moto→2

3. **`rotateTires(...)`**
   - Executa rodízio entre dois pneus do mesmo veículo
   - Registra eventos automáticos
   - Validações de segurança

4. **`replaceTire(...)`**
   - Substitui pneu antigo por novo do estoque
   - Remove pneu antigo (volta ao estoque)
   - Instala novo pneu na posição especificada

5. **`removeTire(...)`**
   - Remove pneu do veículo
   - Atualiza status (Estoque, Manutenção, Recapagem, Descartado)

6. **`calculateCondition($tire)`**
   - Calcula condição baseada no uso:
     - **Novo**: < 30% de uso
     - **Bom**: 30-70% de uso
     - **Atenção**: 70-90% de uso
     - **Crítico**: > 90% de uso

7. **`updateVehicleTiresKm(...)`**
   - Atualiza quilometragem de todos os pneus de um veículo
   - Recalcula condições automaticamente
   - **Integração**: Chamar ao finalizar corrida no diário de bordo

## Layouts de Pneus Disponíveis

### 1. Carro/Caminhonete (4 Pneus)
```
Posição 1: Dianteiro Esquerdo (DE)
Posição 2: Dianteiro Direito (DD)
Posição 3: Traseiro Esquerdo (TE)
Posição 4: Traseiro Direito (TD)
```

### 2. Van (6 Pneus)
```
Posição 1: Dianteiro Esquerdo
Posição 2: Dianteiro Direito
Posição 3: Traseiro Esquerdo Externo
Posição 4: Traseiro Esquerdo Interno
Posição 5: Traseiro Direito Interno
Posição 6: Traseiro Direito Externo
```

### 3. Caminhão Truck (10 Pneus)
```
Posição 1-2: Dianteiros
Posição 3-6: Traseiro Eixo 1 (4 pneus)
Posição 7-10: Traseiro Eixo 2 (4 pneus)
```

### 4. Ônibus (6 Pneus)
Similar à Van, com traseira dupla

### 5. Motocicleta (2 Pneus)
```
Posição 1: Dianteiro
Posição 2: Traseiro
```

## Rotas da Aplicação

```php
// Dashboard
GET /tires                          → Dashboard principal

// Gestão de Veículos
GET /tires/vehicles                 → Lista de veículos
GET /tires/vehicles/{vehicle}       → Diagrama interativo

// Estoque
GET /tires/stock                    → Lista de pneus em estoque
GET /tires/create                   → Formulário de cadastro
POST /tires/store                   → Salvar novo pneu

// Ações de Manutenção (AJAX)
POST /tires/rotate                  → Executar rodízio
POST /tires/replace                 → Trocar pneu
POST /tires/remove                  → Remover pneu
POST /tires/register-event          → Registrar evento

// Histórico
GET /tires/history/{tire}           → Histórico completo

// API
GET /api/tires/{tire}               → Dados do pneu (JSON)
```

## Códigos de Cores

### Condições:
- 🟢 **Verde**: Novo (0-30% de uso)
- 🔵 **Azul**: Bom (30-70% de uso)
- 🟡 **Amarelo**: Atenção (70-90% de uso)
- 🔴 **Vermelho**: Crítico (>90% de uso)

### Status:
- **Em Estoque**: Cinza
- **Em Uso**: Verde
- **Em Manutenção**: Amarelo
- **Recapagem**: Azul
- **Descartado**: Vermelho

## Integrações Recomendadas

### 1. Diário de Bordo
Ao finalizar uma corrida, atualizar KM dos pneus:

```php
// No RunController, método storeFinishRun()
$tireService = app(\App\Services\TireService::class);
$tireService->updateVehicleTiresKm(
    $vehicle->id,
    $request->final_km,
    $run->initial_km
);
```

### 2. Alertas Automáticos
Criar notificações quando pneus atingirem status crítico

### 3. Relatórios
- Custo médio por KM rodado
- Tempo médio de vida dos pneus por marca
- Previsão de substituição

## Segurança e Validações

✅ **Todas as ações são auditadas** - registradas em `tire_events`
✅ **Validação de unicidade** - número de série único
✅ **Validação de disponibilidade** - pneu deve estar disponível para instalação
✅ **Transações de banco** - garantem consistência dos dados
✅ **Autenticação obrigatória** - todas as rotas protegidas por middleware auth

## Próximos Passos (Melhorias Futuras)

1. **Relatórios em PDF**
   - Histórico de manutenção
   - Relatório de custos
   
2. **Notificações Push**
   - Alertas de pneus críticos
   - Lembretes de rodízio programado

3. **Integração com Fornecedores**
   - Solicitação automática de cotação
   - Controle de garantia

4. **Dashboard de Custos**
   - ROI por marca de pneu
   - Análise de custo-benefício

5. **Aplicativo Mobile**
   - Registro de eventos em campo
   - Fotos dos pneus

## Como Usar

### Fluxo Básico de Operação:

1. **Cadastrar Pneu** → `/tires/create`
2. **Visualizar Estoque** → `/tires/stock`
3. **Selecionar Veículo** → `/tires/vehicles`
4. **Clicar no Pneu** → Abrir modal de ações
5. **Executar Ação** → Rodízio, Troca, etc.
6. **Acompanhar Histórico** → `/tires/history/{id}`

### Exemplo: Fazer Rodízio

1. Acesse o veículo em `/tires/vehicles/{id}`
2. Clique no primeiro pneu
3. Selecione "Fazer Rodízio"
4. Escolha o segundo pneu
5. Informe o KM atual do veículo
6. Confirme a operação
7. Sistema atualiza posições e registra evento

## Suporte

Para dúvidas ou problemas:
- Verifique o histórico de eventos do pneu
- Consulte os logs de auditoria em `tire_events`
- Validações de negócio estão em `TireService.php`

---

**Módulo implementado com sucesso! 🎉**

