# Sistema de Cotação de Combustível - Implementação Completa

## 📋 Resumo da Implementação

Foi implementado um sistema completo de cotação de combustível com as seguintes características:

### ✅ Funcionalidades Implementadas

#### 1. **Estrutura de Dados**
- **Postos com Todos os Combustíveis**: Cada posto adicionado exibe automaticamente todos os tipos de combustível cadastrados no sistema
- **Preços Opcionais**: É possível deixar campos em branco ou com valor "0" para combustíveis não disponíveis no posto
- **2 Imagens por Posto**: Cada preço de combustível por posto pode ter até 2 imagens como comprovante
- **Remoção de Imagens**: Botão "X" em cada imagem para remoção antes do envio

#### 2. **Configurações Personalizadas** (Página de Settings)
Acessível apenas para Gestores Gerais através da sidebar.

##### **Métodos de Cálculo Personalizados**
- Criar métodos de cálculo por tipo de combustível
- Tipos disponíveis:
  - **Média Simples**: Média aritmética dos preços coletados
  - **Média Ponderada**: Para cálculos com pesos diferentes
  - **Personalizado**: Com fórmula customizada
- Campos:
  - Nome do método
  - Tipo de cálculo
  - Fórmula (opcional)
  - Status (Ativo/Inativo)
  - Ordem de prioridade

##### **Descontos Personalizados**
- Criar descontos por tipo de combustível
- Tipos disponíveis:
  - **Porcentagem**: Desconto em percentual
  - **Valor Fixo**: Desconto em valor absoluto
  - **Personalizado**: Lógica customizada
- Campos:
  - Nome do desconto
  - Tipo de desconto
  - Porcentagem (%)
  - Valor fixo (R$)
  - Status (Ativo/Inativo)
  - Ordem de prioridade

#### 3. **Preços de Bomba (Opcional)**
- Seção separada para registrar preços reais praticados nos postos
- Funciona com a mesma lógica de adicionar/remover
- Campos por registro:
  - Posto de combustível
  - Tipo de combustível
  - Preço praticado
  - Comprovante (imagem opcional)
- Permite múltiplos registros de preços de bomba

#### 4. **Fluxo de Criação de Cotação**

**Passo 1: Informações Básicas**
- Nome da cotação
- Data da cotação
- Observações (opcional)

**Passo 2: Adicionar Postos e Preços**
- Botão "Adicionar Posto"
- Seleção do posto
- Grid automático com todos os combustíveis
- Input de preço para cada combustível
- Upload de 2 imagens por posto (opcional)
- Botão "X" para remover imagens
- Botão para remover o posto inteiro

**Passo 3: Adicionar Preços de Bomba (Opcional)**
- Botão "Adicionar Preço de Bomba"
- Seleção do posto
- Seleção do combustível
- Input do preço de bomba
- Upload de comprovante (opcional)
- Botão para remover o registro

**Passo 4: Salvar**
- Validação automática
- Cálculo de médias usando configurações definidas
- Aplicação de descontos configurados
- Redirecionamento para visualização

#### 5. **Cálculo Automático**
O sistema calcula automaticamente:
- **Média de preços** por combustível (usando método configurado)
- **Desconto aplicado** (usando configuração ativa)
- **Preço final** após desconto
- Ignora valores em branco ou zero

### 🗂️ Estrutura de Arquivos Criados/Modificados

#### **Models**
- `app/Models/FuelCalculationMethod.php` - Métodos de cálculo
- `app/Models/FuelDiscountSetting.php` - Descontos personalizados
- `app/Models/FuelQuotationPrice.php` - Preços coletados
- `app/Models/FuelType.php` - Atualizado com relacionamentos

#### **Controllers**
- `app/Http/Controllers/FuelQuotationController.php` - CRUD de cotações
- `app/Http/Controllers/FuelQuotationSettingsController.php` - Configurações

#### **Views**
- `resources/views/fuel-quotations/create.blade.php` - Formulário de criação
- `resources/views/fuel-quotations/settings.blade.php` - Página de configurações

#### **Migrations**
- `2025_10_09_145210_create_fuel_calculation_methods_and_settings_tables.php`

#### **Rotas**
Todas as rotas configuradas em `routes/web.php`:
- `fuel-quotations.index` - Listar cotações
- `fuel-quotations.create` - Criar nova cotação
- `fuel-quotations.store` - Salvar cotação
- `fuel-quotations.show` - Visualizar cotação
- `fuel-quotations.settings` - Configurações (apenas gestores gerais)
- `fuel-quotations.settings.calculation-methods.*` - CRUD de métodos
- `fuel-quotations.settings.discount-settings.*` - CRUD de descontos
- `fuel-quotations.delete-image` - Remover imagem

### 🎨 Interface do Usuário

#### **Formulário de Criação**
- Design responsivo com Tailwind CSS
- Suporte a tema escuro
- Alpine.js para interatividade
- Preview de imagens em tempo real
- Validação no frontend e backend
- Feedback visual para ações do usuário

#### **Página de Configurações**
- Tabs para alternar entre Métodos e Descontos
- Cards organizados por tipo de combustível
- Modais para criar/editar configurações
- Badges de status (Ativo/Inativo)
- Ordenação visual

### 🔐 Permissões
- **Configurações**: Apenas Gestores Gerais (`isGeneralManager()`)
- **Criação de Cotações**: Usuários autenticados
- **Visualização**: Todos os usuários autenticados

### 📊 Tecnologias Utilizadas
- **Backend**: Laravel 11
- **Frontend**: Alpine.js + Tailwind CSS
- **Banco de Dados**: PostgreSQL/MySQL
- **Upload**: Laravel Storage (public disk)
- **Validação**: Laravel Validation

### 🚀 Como Usar

#### **1. Configurar Métodos e Descontos** (Gestores Gerais)
1. Acessar "Cotação de Combustível" > "Configurações" na sidebar
2. Na aba "Métodos de Cálculo", criar métodos personalizados para cada combustível
3. Na aba "Descontos Personalizados", criar descontos para cada combustível
4. Definir qual método/desconto está ativo

#### **2. Criar Nova Cotação**
1. Acessar "Cotação de Combustível" > "Nova Cotação"
2. Preencher informações básicas
3. Clicar em "Adicionar Posto"
4. Selecionar o posto
5. Preencher preços dos combustíveis disponíveis
6. Adicionar imagens (opcional)
7. Repetir para outros postos
8. Opcionalmente, adicionar preços de bomba
9. Clicar em "Salvar Cotação"

#### **3. Visualizar Resultado**
- O sistema calculará automaticamente as médias
- Aplicará os descontos configurados
- Exibirá tabela comparativa com preços de bomba (se fornecidos)

### 📝 Observações Importantes

1. **Preços Zerados**: Valores em branco, "0" ou nulos são ignorados no cálculo
2. **Imagens**: Máximo de 2 imagens por preço de combustível (5MB cada)
3. **Configurações**: Apenas o primeiro método/desconto ativo é usado no cálculo
4. **Ordenação**: Use o campo "ordem" para definir prioridade quando há múltiplas configurações

### 🔄 Fluxo de Dados

```
1. Usuário cria cotação
   ↓
2. Sistema salva preços coletados (fuel_quotation_prices)
   ↓
3. Sistema busca configurações ativas por combustível
   ↓
4. Calcula média usando método configurado
   ↓
5. Aplica desconto configurado
   ↓
6. Salva resultado final (fuel_quotation_discounts)
   ↓
7. Salva preços de bomba (fuel_pump_prices) se fornecidos
```

### ✨ Melhorias Futuras Sugeridas

1. Gráficos comparativos de preços
2. Histórico de cotações por posto
3. Alertas de variação de preços
4. Exportação para Excel/PDF
5. API para integração com outros sistemas
6. Notificações automáticas
7. Aprovação de cotações por gestores

### 🐛 Tratamento de Erros

- Validação completa no frontend e backend
- Mensagens de erro claras e em português
- Rollback automático em caso de falha
- Logs detalhados para debugging

---

**Data de Implementação**: 09 de Janeiro de 2025  
**Status**: ✅ Completo e Funcional  
**Versão**: 1.0.0

