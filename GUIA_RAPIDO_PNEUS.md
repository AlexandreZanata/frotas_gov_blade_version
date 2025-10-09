# Guia Rápido - Módulo de Troca de Pneus

## ✅ Instalação Completa

O módulo de Troca de Pneus foi implementado com sucesso no sistema!

## 📋 O que foi criado:

### 1. Banco de Dados
- ✅ Tabela `tires` - Pneus físicos
- ✅ Tabela `tire_events` - Histórico de eventos
- ✅ Tabela `vehicle_tire_layouts` - Layouts de veículos
- ✅ Migrações executadas com sucesso
- ✅ Layouts de pneus populados (Carro, Van, Caminhão, Ônibus, Moto)

### 2. Backend
- ✅ `TireController` - Controlador principal
- ✅ `TireService` - Lógica de negócio
- ✅ Modelos: `Tire`, `TireEvent`, `VehicleTireLayout`
- ✅ Relacionamentos adicionados ao modelo `Vehicle`

### 3. Frontend
- ✅ Dashboard principal (`/tires`)
- ✅ Lista de veículos (`/tires/vehicles`)
- ✅ Diagrama interativo (`/tires/vehicles/{id}`)
- ✅ Estoque de pneus (`/tires/stock`)
- ✅ Cadastro de pneus (`/tires/create`)
- ✅ Histórico de eventos (`/tires/history/{id}`)

### 4. Navegação
- ✅ Menu "Manutenção" adicionado na barra superior
- ✅ Submenu "Troca de Pneus" dentro de Manutenção
- ✅ Menu responsivo para mobile configurado

## 🚀 Como Acessar

1. **Acesse o sistema** e faça login
2. **Clique em "Manutenção"** na barra superior
3. **Selecione "Troca de Pneus"**

## 📌 Primeiros Passos

### 1. Cadastrar Primeiro Pneu
```
1. Vá em: Manutenção → Troca de Pneus → Novo Pneu
2. Preencha os dados:
   - Tipo de Pneu: Selecione do inventário
   - Marca: Ex: Michelin
   - Modelo: Ex: 175/70 R13
   - Número de Série: Único para cada pneu
   - Vida Útil: Ex: 40000 km
3. Salvar
```

### 2. Instalar Pneu em Veículo
```
1. Vá em: Manutenção → Troca de Pneus → Veículos
2. Selecione um veículo
3. Clique em uma posição vazia no diagrama
4. Escolha "Instalar Pneu do Estoque"
5. Selecione o pneu e informe o KM atual
```

### 3. Fazer Rodízio
```
1. Acesse o diagrama do veículo
2. Clique no primeiro pneu
3. Selecione "Fazer Rodízio"
4. Escolha o segundo pneu
5. Informe o KM atual e confirme
```

## 🎨 Código de Cores

- 🟢 **Verde** - Novo (0-30% de uso)
- 🔵 **Azul** - Bom (30-70% de uso)
- 🟡 **Amarelo** - Atenção (70-90% de uso)
- 🔴 **Vermelho** - Crítico (>90% de uso)

## 🔗 Rotas Principais

```
/tires                          → Dashboard
/tires/vehicles                 → Lista de veículos
/tires/vehicles/{id}            → Diagrama interativo
/tires/stock                    → Estoque
/tires/create                   → Cadastrar pneu
/tires/history/{id}             → Histórico
```

## 📊 Funcionalidades

### Dashboard
- Estatísticas em tempo real
- Alertas de pneus críticos
- Vida útil média da frota
- Lista de ações imediatas

### Gestão Visual
- Diagrama interativo por tipo de veículo
- Clique direto nos pneus para ações
- Visualização de condição com cores
- Tooltips informativos

### Ações Disponíveis
- ✅ Rodízio (trocar posições)
- ✅ Trocar pneu
- ✅ Remover pneu
- ✅ Registrar evento
- ✅ Ver histórico completo

### Estoque
- Filtros por condição
- Busca rápida
- Barras de progresso de uso
- Acesso ao histórico

## 🔄 Integração com Diário de Bordo

Para atualizar automaticamente a quilometragem dos pneus ao finalizar uma corrida:

```php
// Em RunController::storeFinishRun()
$tireService = app(\App\Services\TireService::class);
$tireService->updateVehicleTiresKm(
    $vehicle->id,
    $request->final_km,
    $run->initial_km
);
```

## 📝 Layouts Disponíveis

- **Carro/Caminhonete**: 4 pneus
- **Van**: 6 pneus
- **Caminhão Truck**: 10 pneus
- **Ônibus**: 6 pneus
- **Motocicleta**: 2 pneus

## ⚙️ Configuração Adicional

### Criar Item de Inventário para Pneus (se necessário)
```sql
-- Execute no banco de dados se ainda não tiver categoria de pneus
INSERT INTO inventory_item_categories (id, name, description, created_at, updated_at)
VALUES (uuid(), 'Pneus', 'Pneus para veículos da frota', NOW(), NOW());
```

## 🎯 Testes Recomendados

1. ✅ Cadastrar um pneu novo
2. ✅ Instalar pneu em um veículo
3. ✅ Fazer rodízio entre dois pneus
4. ✅ Trocar um pneu
5. ✅ Verificar histórico de eventos
6. ✅ Remover pneu para manutenção
7. ✅ Visualizar dashboard com estatísticas

## 📚 Documentação Completa

Consulte o arquivo `MODULO_TROCA_PNEUS_COMPLETO.md` para:
- Detalhes técnicos
- Estrutura do banco de dados
- Exemplos de código
- Fluxos de operação
- Melhorias futuras

## 🐛 Solução de Problemas

### Menu não aparece?
- Verifique se está logado
- Limpe o cache: `php artisan view:clear`

### Erro ao cadastrar pneu?
- Verifique se tem categoria "Pneus" no inventário
- Número de série deve ser único

### Diagrama não carrega?
- Verifique se o seeder foi executado
- Execute: `php artisan db:seed --class=VehicleTireLayoutSeeder`

## ✨ Pronto para Usar!

O módulo está **100% funcional** e pronto para uso em produção.

Acesse: **Manutenção → Troca de Pneus**

---
**Implementado em**: 09/10/2025
**Status**: ✅ Operacional

