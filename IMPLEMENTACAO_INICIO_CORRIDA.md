# Implementação: Sistema de Início de Corrida Após Checklist

## 📋 Resumo
Sistema completo para iniciar uma corrida após preencher o checklist do veículo, com validação automática de quilometragem baseada nas últimas 10 corridas.

## 🎯 Funcionalidades Implementadas

### 1. **KM Atual Pré-preenchido**
- O sistema busca automaticamente o último KM finalizado da corrida anterior do veículo
- O campo vem pré-preenchido, mas pode ser editado pelo motorista se necessário
- Se não houver corridas anteriores, o campo inicia com 0

### 2. **Validação de KM Máximo Permitido**
Após as primeiras 10 corridas do veículo, o sistema ativa automaticamente:

#### Cálculo da Média:
- Calcula a média de KM rodados nas últimas 10 corridas
- Aplica um percentual de ajuste (padrão: 100%, configurável)
- Define o KM máximo permitido para a próxima corrida

#### Exemplo:
```
Últimas 10 corridas:
- Corrida 1: 50 km
- Corrida 2: 45 km
- Corrida 3: 60 km
- ... (média total: 52 km)

KM máximo permitido = 52 km × 100% = 52 km
```

#### Validação:
- O sistema verifica se a diferença entre o KM atual e o último KM está dentro do limite
- Se exceder, exibe mensagem de erro e impede o início da corrida
- Se o KM for menor que o último registrado, também impede

### 3. **Campo Destino Obrigatório**
- Campo de texto obrigatório para informar o destino da viagem
- Validação no frontend e backend
- Máximo de 255 caracteres

## 📂 Arquivos Modificados

### 1. **LogbookService.php**
**Localização:** `app/Services/LogbookService.php`

**Novos Métodos Adicionados:**

```php
// Calcula a média de quilometragem das últimas N corridas
public function getAverageKmFromLastRuns(string $vehicleId, int $numberOfRuns = 10): ?float

// Calcula o KM máximo permitido baseado na média das corridas
public function getMaxAllowedKm(string $vehicleId, float $percentageAdjustment = 100): ?array

// Valida se o KM inicial está dentro do limite permitido
public function validateStartKm(string $vehicleId, int $startKm, float $percentageAdjustment = 100): array
```

### 2. **RunController.php**
**Localização:** `app/Http/Controllers/RunController.php`

**Método Atualizado:**
```php
public function startRun(Run $run)
{
    $this->authorize('update', $run);
    
    $lastKm = $this->logbookService->getLastKm($run->vehicle_id);
    $maxAllowedData = $this->logbookService->getMaxAllowedKm($run->vehicle_id, 100);
    
    return view('logbook.start-run', compact('run', 'lastKm', 'maxAllowedData'));
}
```

### 3. **RunStartRequest.php**
**Localização:** `app/Http/Requests/RunStartRequest.php`

**Validações Implementadas:**
- `start_km`: obrigatório, inteiro, mínimo 0
- `destination`: obrigatório, string, máximo 255 caracteres
- `origin`: opcional, string, máximo 255 caracteres
- Validação customizada que verifica o limite de KM

### 4. **start-run.blade.php**
**Localização:** `resources/views/logbook/start-run.blade.php`

**Novos Componentes Visuais:**
- Alert azul: Exibe o último KM registrado
- Alert verde: Exibe informações sobre o limite de KM calculado (quando há 10+ corridas)
- Alert amarelo: Exibe mensagem sobre calibração (quando há menos de 10 corridas)
- Alert cinza: Notas importantes para o usuário

## 🔄 Fluxo de Uso

### Passo a Passo:

1. **Seleção do Veículo**
   - Usuário seleciona o veículo disponível
   - Sistema salva na sessão (sem criar corrida ainda)

2. **Checklist do Veículo**
   - Usuário preenche o checklist de verificação
   - Sistema cria a corrida e salva o checklist
   - Redireciona para o início da corrida

3. **Início da Corrida** ⭐ (Nova Implementação)
   - Sistema busca o último KM do veículo
   - Sistema calcula o limite de KM permitido (se houver 10+ corridas)
   - Exibe informações visuais sobre limites e última quilometragem
   - Usuário preenche:
     - **KM Atual** (pré-preenchido, editável)
     - **Destino** (obrigatório)
     - **Origem** (opcional)
   - Sistema valida se o KM está dentro do limite
   - Se válido, inicia a corrida

4. **Finalização da Corrida**
   - Usuário informa o KM final
   - Sistema finaliza a corrida

## 📊 Validações Implementadas

### Frontend (Blade):
- Campos obrigatórios marcados com asterisco (*)
- Placeholders informativos
- Mensagens de ajuda abaixo dos campos
- Alerts coloridos com informações contextuais

### Backend (Request):
```php
'start_km' => ['required', 'integer', 'min:0']
'destination' => ['required', 'string', 'max:255']
'origin' => ['nullable', 'string', 'max:255']
```

### Validação Customizada:
- KM não pode ser menor que o último registrado
- KM não pode exceder o limite calculado (após 10 corridas)
- Mensagens de erro descritivas

## 🎨 Interface do Usuário

### Cores e Estados:

| Cor | Significado | Quando Aparece |
|-----|-------------|----------------|
| Azul | Informação | Sempre (último KM registrado) |
| Verde | Sucesso/Limite Ativo | Quando há 10+ corridas |
| Amarelo | Atenção/Calibração | Quando há menos de 10 corridas |
| Vermelho | Erro | Quando validação falha |
| Cinza | Notas Importantes | Sempre |

## 🔧 Configurações

### Percentual de Ajuste do Limite:
O percentual de ajuste pode ser configurado no controller:

```php
// 100% da média (padrão)
$maxAllowedData = $this->logbookService->getMaxAllowedKm($run->vehicle_id, 100);

// 200% da média (mais permissivo)
$maxAllowedData = $this->logbookService->getMaxAllowedKm($run->vehicle_id, 200);

// 50% da média (mais restritivo)
$maxAllowedData = $this->logbookService->getMaxAllowedKm($run->vehicle_id, 50);
```

### Número de Corridas para Calibração:
Por padrão, o sistema usa as últimas 10 corridas. Isso pode ser ajustado no método:

```php
public function getAverageKmFromLastRuns(string $vehicleId, int $numberOfRuns = 10): ?float
```

## 📝 Exemplos de Uso

### Cenário 1: Veículo com menos de 10 corridas
```
Tela exibe:
- Último KM: 1.500 km (alert azul)
- "Limite em calibração: Complete mais 7 corridas..." (alert amarelo)
- Campo KM Atual: 1.500 (pré-preenchido)
- Campo Destino: (vazio)

Usuário pode inserir qualquer KM >= 1.500
```

### Cenário 2: Veículo com 10+ corridas
```
Tela exibe:
- Último KM: 5.000 km (alert azul)
- "Média por corrida: 52 km | Máximo permitido: 52 km" (alert verde)
- Campo KM Atual: 5.000 (pré-preenchido)
- Campo Destino: (vazio)

Usuário insere: 5.060 km
Sistema valida: APROVADO (diferença de 60 km < 52 km? NÃO)
Sistema retorna erro: "A diferença de 60 km excede o máximo de 52 km"

Usuário corrige para: 5.050 km
Sistema valida: APROVADO (diferença de 50 km < 52 km)
Corrida iniciada com sucesso!
```

### Cenário 3: KM menor que o anterior
```
Último KM: 3.000 km
Usuário insere: 2.950 km
Sistema retorna erro: "O KM atual (2.950 km) não pode ser menor que o último registrado (3.000 km)"
```

## 🚀 Benefícios

1. **Prevenção de Fraudes**
   - Detecta KMs inconsistentes automaticamente
   - Baseado em dados reais do veículo

2. **Facilidade de Uso**
   - KM pré-preenchido economiza tempo
   - Validações claras e informativas

3. **Inteligência Adaptativa**
   - Sistema aprende o padrão de uso de cada veículo
   - Ajusta automaticamente após 10 corridas

4. **Transparência**
   - Usuário vê claramente os limites e justificativas
   - Informações visuais e contextuais

## 🧪 Testes Recomendados

1. **Teste com veículo novo (0 corridas)**
   - Verificar se permite qualquer KM
   - Verificar mensagem de calibração

2. **Teste com veículo com 5 corridas**
   - Verificar mensagem de calibração
   - Verificar contagem correta

3. **Teste com veículo com 10+ corridas**
   - Verificar cálculo da média
   - Verificar limite máximo
   - Testar validação de excesso

4. **Teste de validação de KM menor**
   - Inserir KM menor que o último
   - Verificar mensagem de erro

5. **Teste de campo destino**
   - Tentar enviar sem destino
   - Verificar mensagem de erro

## 📌 Observações Importantes

- O cálculo do limite é baseado na **diferença de KM entre corridas**, não no KM absoluto
- O percentual de ajuste pode ser configurado por secretaria/gestor no futuro
- A validação ocorre tanto no frontend quanto no backend
- O sistema mantém compatibilidade com corridas antigas

## 🔮 Melhorias Futuras Sugeridas

1. **Configuração por Secretaria**
   - Permitir que cada secretaria defina seu percentual de ajuste
   - Configurar número de corridas para calibração

2. **Relatório de Anomalias**
   - Dashboard com corridas que excederam limites
   - Alertas para gestores

3. **Integração com GPS**
   - Validar KM com dados de GPS (se disponível)

4. **Notificações**
   - Notificar gestor quando limite for excedido
   - Notificar quando veículo completar 10 corridas (limite ativado)

---

**Data da Implementação:** 08/10/2025  
**Desenvolvedor:** GitHub Copilot  
**Versão:** 1.0

