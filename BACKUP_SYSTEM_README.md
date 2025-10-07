 # Sistema de Backup e Exclusão com Confirmação

## 📋 Funcionalidades Implementadas

### 1. **Mensagens de Confirmação Responsivas**
- ✅ Modal de confirmação adaptável a diferentes tamanhos de tela
- ✅ Sem scroll lateral - todo conteúdo quebra e se adapta corretamente
- ✅ Suporte para texto com quebra de linha (whitespace-pre-wrap)

### 2. **Confirmação com Texto "Eu estou ciente"**
- ✅ Campo de texto obrigatório para confirmação de exclusões críticas
- ✅ Botão desabilitado até que o usuário digite exatamente "Eu estou ciente"
- ✅ Validação case-insensitive (maiúsculas/minúsculas não importam)

### 3. **Sistema de Backup em PDF (TCPDF)**
- ✅ Geração automática de PDF com todos os dados relacionados
- ✅ Checkbox opcional para gerar backup antes da exclusão
- ✅ PDFs salvos em `storage/app/backups/`
- ✅ Inclui: Ordens de Serviço, Abastecimentos, Viagens, Multas, Defeitos, Transferências

### 4. **Deletes em Cascata**
- ✅ Configuração de `onDelete('cascade')` em todas as foreign keys
- ✅ Exclusão automática de todos os registros relacionados
- ✅ Tabelas afetadas:
  - service_orders
  - fuelings
  - runs
  - fines
  - defect_reports
  - vehicle_transfers

### 5. **Página de Relatórios/Backups**
- ✅ Menu "Relatórios" na sidebar
- ✅ Listagem de todos os backups gerados
- ✅ Download de PDFs
- ✅ Exclusão de backups antigos

### 6. **Pesquisa Inteligente com Debounce**
- ✅ Componente `<x-ui.searchable-table>` reutilizável
- ✅ Debounce de 500ms para evitar sobrecarga
- ✅ Pesquisa em múltiplos campos
- ✅ Paginação integrada
- ✅ Não sobrecarrega o banco de dados

## 🚀 Como Usar

### Excluir um Veículo com Backup

1. Acesse a página de detalhes do veículo
2. Clique em "Excluir Veículo"
3. Leia a mensagem de aviso completa
4. **[Opcional]** Marque "Gerar backup em PDF"
5. Digite "Eu estou ciente" no campo de confirmação
6. Clique em "Confirmar"

### Visualizar Backups

1. Acesse o menu "Relatórios" na sidebar
2. Use a pesquisa para filtrar backups
3. Clique no ícone de download para baixar o PDF
4. Ou exclua backups antigos usando o ícone de lixeira

### Usar o Componente em Outras Páginas

```blade
<x-ui.confirm-form 
    :action="route('sua-rota.destroy', $item)" 
    method="DELETE" 
    message="Sua mensagem de aviso aqui. Pode incluir:
    
    • Múltiplas linhas
    • Bullets
    • Informações importantes
    
    Esta ação é irreversível!" 
    title="Título do Modal" 
    button-class="seu-css-aqui" 
    icon="trash" 
    :icon-only="false"
    :require-backup="true"
    :require-confirmation-text="true"
    confirmation-text="Eu estou ciente">
    Texto do Botão
</x-ui.confirm-form>
```

### Usar Pesquisa Inteligente em Tabelas

```blade
<x-ui.searchable-table 
    :headers="['Coluna 1', 'Coluna 2', 'Ações']"
    :searchable="true"
    search-placeholder="Pesquisar..."
    :search-value="$search ?? ''"
    :search-route="route('sua-rota.index')"
    :pagination="$items">
    
    @foreach($items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <!-- ... -->
        </tr>
    @endforeach
</x-ui.searchable-table>
```

## 🔧 Arquivos Criados/Modificados

### Novos Arquivos:
- `app/Models/BackupReport.php` - Model para backups
- `app/Services/BackupPdfService.php` - Serviço de geração de PDF
- `app/Http/Controllers/BackupReportController.php` - Controller de backups
- `resources/views/backup-reports/index.blade.php` - Página de relatórios
- `resources/views/components/ui/searchable-table.blade.php` - Componente de tabela com pesquisa
- `database/migrations/*_create_backup_reports_table.php`
- `database/migrations/*_add_cascade_deletes_to_vehicle_related_tables.php`

### Arquivos Modificados:
- `resources/views/components/ui/confirm-form.blade.php` - Adicionado backup e confirmação de texto
- `resources/views/vehicles/show.blade.php` - Atualizado botão de exclusão
- `app/Models/Vehicle.php` - Adicionados relacionamentos hasMany
- `app/Http/Controllers/VehicleController.php` - Lógica de backup no destroy
- `routes/web.php` - Rotas de backup reports
- `resources/views/layouts/navigation-links.blade.php` - Menu de Relatórios
- `composer.json` - Adicionada biblioteca TCPDF

## 📦 Dependências Instaladas

```bash
composer require tecnickcom/tcpdf
```

## 🗄️ Banco de Dados

### Nova Tabela: backup_reports
- `id` (UUID)
- `user_id` (quem gerou o backup)
- `entity_type` (Vehicle, ServiceOrder, etc)
- `entity_id` (ID do registro deletado)
- `entity_name` (nome/identificador legível)
- `file_path` (caminho do PDF)
- `file_name` (nome do arquivo)
- `file_size` (tamanho em bytes)
- `description` (descrição do backup)
- `metadata` (JSON com dados adicionais)
- `created_at` / `updated_at`

## ⚙️ Configurações

Os backups são salvos em: `storage/app/backups/`

Para garantir que o diretório existe:
```bash
mkdir -p storage/app/backups
chmod -R 775 storage/app/backups
```

## 🎨 Características do Sistema

### Responsividade
- Modal adapta tamanho em telas pequenas
- Mensagens quebram automaticamente
- Sem scroll lateral
- Botões com tamanho responsivo

### Performance
- Debounce de 500ms na pesquisa
- Paginação eficiente
- Queries otimizadas com eager loading

### Segurança
- Confirmação dupla (checkbox + texto)
- Mensagens claras sobre irreversibilidade
- Backup opcional antes de deletar
- Cascade delete configurado no banco

## 📝 Exemplo de PDF Gerado

O PDF inclui:
- ✅ Cabeçalho com logo/título
- ✅ Informações completas do veículo
- ✅ Lista de Ordens de Serviço
- ✅ Histórico de Abastecimentos
- ✅ Registro de Viagens
- ✅ Multas aplicadas
- ✅ Relatórios de Defeitos
- ✅ Transferências entre secretarias
- ✅ Rodapé com data e usuário

## 🔄 Próximos Passos (Opcional)

Para estender o sistema para outras entidades:

1. Adicione método no `BackupPdfService`:
```php
public function generateServiceOrderBackup(ServiceOrder $order): BackupReport
{
    // Similar ao generateVehicleBackup
}
```

2. Use no controller:
```php
if ($request->has('create_backup')) {
    $backupService = new BackupPdfService();
    $backupService->generateServiceOrderBackup($order);
}
```

3. Adicione cascade delete na migration

## ✅ Checklist de Implementação

- [x] Instalar TCPDF
- [x] Criar model BackupReport
- [x] Criar serviço de geração de PDF
- [x] Criar controller de backups
- [x] Atualizar componente confirm-form
- [x] Criar componente searchable-table
- [x] Criar página de relatórios
- [x] Adicionar rotas
- [x] Adicionar menu na sidebar
- [x] Configurar cascade deletes
- [x] Testar responsividade
- [x] Executar migrations

---

**Sistema implementado com sucesso! 🎉**

