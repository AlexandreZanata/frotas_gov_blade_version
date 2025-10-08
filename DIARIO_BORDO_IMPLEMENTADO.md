# Módulo Diário de Bordo - Documentação

## Visão Geral

O módulo **Diário de Bordo** foi implementado seguindo rigorosamente os princípios arquiteturais do projeto:

- ✅ **100% Componentizado** - Todas as views utilizam apenas componentes Blade reutilizáveis
- ✅ **Validação Abstraída** - Form Requests para todas as validações
- ✅ **Schema Imutável** - Utiliza migrations e models existentes sem alterações

## Estrutura Criada

### 1. Form Requests (Validação)
- `RunStartRequest.php` - Validação do início da corrida com verificação de KM
- `RunFinishRequest.php` - Validação do fim da corrida com limite de distância
- `ChecklistRequest.php` - Validação do checklist com notas obrigatórias para problemas
- `FuelingRequest.php` - Validação de abastecimento (credenciado ou manual)

### 2. Service Layer
- `LogbookService.php` - Toda lógica de negócio centralizada:
  - Persistência de navegação
  - Verificação de disponibilidade de veículos
  - Estado do checklist
  - Gerenciamento de corridas

### 3. Controller
- `RunController.php` - Orquestra o fluxo completo do diário de bordo

### 4. Policy
- `RunPolicy.php` - Autorização (view, update, delete)

### 5. Componentes UI Criados
- `vehicle-card.blade.php` - Card de seleção de veículo com Alpine.js
- `checklist-item.blade.php` - Item de checklist interativo (OK/Atenção/Problema)
- `progress-steps.blade.php` - Indicador visual de progresso
- `km-input.blade.php` - Input específico para quilometragem

### 6. Views do Fluxo
1. `logbook/index.blade.php` - Lista de corridas
2. `logbook/select-vehicle.blade.php` - Seleção do veículo
3. `logbook/checklist.blade.php` - Checklist do veículo
4. `logbook/start-run.blade.php` - Iniciar corrida
5. `logbook/finish-run.blade.php` - Finalizar corrida
6. `logbook/fueling.blade.php` - Abastecimento (opcional)
7. `logbook/show.blade.php` - Detalhes da corrida

## Funcionalidades Implementadas

### ✅ 1. Escolha do Veículo
- Busca por prefixo
- Preenchimento automático de placa, nome e secretaria
- Filtragem por secretaria do motorista
- Verificação de disponibilidade em tempo real via AJAX

### ✅ 2. Verificação e Checklist
- Tela de bloqueio quando veículo está em uso
- Checklist com 3 estados (OK, Atenção, Problema)
- Campo obrigatório de descrição para "Problema"
- Estado persistente do checklist (carrega última verificação)
- Notificação ao gestor em caso de problemas

### ✅ 3. Iniciar Corrida
- KM inicial pré-preenchido com KM final da última corrida
- Campo editável para ajustes
- Validação de KM mínimo
- Campos origem e destino

### ✅ 4. Finalizar Corrida
- KM final com validação contra KM inicial
- Limite de autonomia (500km por corrida, configurável)
- Campo opcional para ponto de parada
- Opção de adicionar abastecimento

### ✅ 5. Abastecimento (Opcional)
- **Modo Credenciado**: Posto selecionado, preço automático
- **Modo Manual**: Todos os campos editáveis
- Upload de nota fiscal
- Assinatura digital com canvas
- Notificação ao gestor
- Código público único para consulta

### ✅ 6. Persistência de Navegação
- Estado salvo em sessão
- Redirecionamento automático para etapa correta
- Recuperação de corrida em andamento
- Alertas visuais de corrida ativa

## Como Usar

### 1. Executar o Seeder
```bash
php artisan db:seed --class=ChecklistItemSeeder
```

### 2. Rotas Disponíveis
- `GET /logbook` - Lista de corridas
- `GET /logbook/start` - Ponto de entrada (verifica estado)
- `GET /logbook/select-vehicle` - Seleção de veículo
- `GET /logbook/{run}/checklist` - Checklist
- `GET /logbook/{run}/start-run` - Iniciar corrida
- `GET /logbook/{run}/finish` - Finalizar corrida
- `GET /logbook/{run}/fueling` - Abastecimento
- `GET /logbook/{run}` - Detalhes da corrida

### 3. Fluxo Completo
1. Usuário acessa `/logbook/start`
2. Sistema verifica se há corrida em andamento
3. Se não, redireciona para seleção de veículo
4. Usuário seleciona veículo → Sistema verifica disponibilidade
5. Cria corrida e salva estado "checklist"
6. Preenche checklist → Salva e muda estado para "start_run"
7. Informa KM e destino → Inicia corrida (estado "finish_run")
8. Ao finalizar, informa KM final
9. Opcionalmente registra abastecimento
10. Corrida finalizada, estado limpo

## Validações Implementadas

- KM inicial ≥ KM final da última corrida
- KM final > KM inicial
- Distância máxima por corrida: 500km
- Checklist: Notas obrigatórias quando status = "problema"
- Abastecimento: Assinatura obrigatória
- Veículo: Disponibilidade verificada antes de iniciar

## Próximos Passos (Opcional)

1. Implementar notificações por email/SMS
2. Adicionar relatórios de corridas
3. Dashboard com estatísticas
4. Integração com GPS para rastreamento
5. App mobile para motoristas
6. QR Code para checklist rápido

## Observações Importantes

- Todas as views utilizam componentes reutilizáveis
- Nenhuma migration foi criada ou alterada
- Validações estão todas em Form Requests
- Lógica de negócio isolada no Service
- Alpine.js para interatividade
- Dark mode totalmente suportado
- Responsivo para mobile
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChecklistItem;

class ChecklistItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Nível do Óleo',
                'description' => 'Verificar o nível do óleo do motor',
            ],
            [
                'name' => 'Calibragem dos Pneus',
                'description' => 'Verificar a pressão de todos os pneus',
            ],
            [
                'name' => 'Água do Radiador',
                'description' => 'Verificar o nível da água do radiador',
            ],
            [
                'name' => 'Freios',
                'description' => 'Testar o funcionamento dos freios',
            ],
            [
                'name' => 'Luzes',
                'description' => 'Verificar faróis, lanternas e setas',
            ],
            [
                'name' => 'Limpadores de Para-brisa',
                'description' => 'Testar os limpadores e nível do líquido',
            ],
            [
                'name' => 'Buzina',
                'description' => 'Testar o funcionamento da buzina',
            ],
            [
                'name' => 'Cintos de Segurança',
                'description' => 'Verificar o estado dos cintos de segurança',
            ],
            [
                'name' => 'Documentação',
                'description' => 'Verificar se toda documentação está no veículo',
            ],
            [
                'name' => 'Combustível',
                'description' => 'Verificar o nível de combustível',
            ],
            [
                'name' => 'Bateria',
                'description' => 'Verificar o estado da bateria',
            ],
            [
                'name' => 'Estepe',
                'description' => 'Verificar a existência e estado do estepe',
            ],
            [
                'name' => 'Macaco e Chave de Roda',
                'description' => 'Verificar a presença das ferramentas',
            ],
            [
                'name' => 'Triângulo',
                'description' => 'Verificar a presença do triângulo de sinalização',
            ],
            [
                'name' => 'Extintor',
                'description' => 'Verificar a presença e validade do extintor',
            ],
        ];

        foreach ($items as $item) {
            ChecklistItem::firstOrCreate(
                ['name' => $item['name']],
                ['description' => $item['description']]
            );
        }
    }
}

