# Sistema de Chat em Tempo Real - Guia de Implementação

## 📱 Características Implementadas

✅ **Chat estilo WhatsApp** com design mobile-first
✅ **WebSocket em tempo real** com Laravel Reverb
✅ **Mensagens instantâneas** sem recarregar a página
✅ **Status de leitura** (enviado, entregue, lido)
✅ **Indicador de digitação** ("fulano está digitando...")
✅ **Status online/offline** dos usuários
✅ **Upload de arquivos e imagens**
✅ **Chats privados** (1-on-1)
✅ **Grupos de chat**
✅ **Notificações de mensagens não lidas**
✅ **Interface responsiva** (mobile/desktop)
✅ **Dark mode** completo

## 🚀 Instalação e Configuração

### 1. Instalar Dependências PHP

```bash
composer require laravel/reverb
```

### 2. Instalar Dependências JavaScript

```bash
npm install --save laravel-echo pusher-js
```

### 3. Configurar Variáveis de Ambiente

Adicione as seguintes variáveis no seu arquivo `.env`:

```env
# Broadcasting
BROADCAST_CONNECTION=reverb

# Reverb (WebSocket Server)
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite (Frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 4. Publicar Configuração do Reverb

```bash
php artisan reverb:install
```

### 5. Executar Migrações

```bash
php artisan migrate
```

### 6. Criar Link de Storage (para anexos)

```bash
php artisan storage:link
```

### 7. Compilar Assets

```bash
npm run build
# ou para desenvolvimento
npm run dev
```

### 8. Iniciar Servidor WebSocket

Em um terminal separado, execute:

```bash
php artisan reverb:start
```

### 9. Iniciar Aplicação

```bash
php artisan serve
```

## 📊 Estrutura do Banco de Dados

### Tabelas Criadas:

- **chat_rooms** - Salas de chat (privadas ou grupos)
- **chat_participants** - Participantes de cada sala
- **chat_messages** - Mensagens enviadas
- **chat_message_read_receipts** - Confirmações de leitura

## 🎯 Como Usar

### Acessar o Chat

Navegue para: `http://localhost:8000/chat`

### Iniciar Nova Conversa

1. Clique no botão **"+"** no canto superior direito
2. Busque o usuário desejado
3. Clique no usuário para iniciar a conversa

### Enviar Mensagens

- Digite a mensagem no campo de texto
- Pressione **Enter** ou clique no botão de enviar
- Use **Shift+Enter** para quebrar linha

### Enviar Arquivos/Imagens

1. Clique no ícone de clipe 📎
2. Selecione o arquivo (máx. 10MB)
3. Envie junto com ou sem texto

### Status de Leitura

- ✓ Cinza = Enviada
- ✓✓ Cinza = Entregue
- ✓✓ Azul = Lida

## 🔧 Funcionalidades Técnicas

### WebSocket Events

**Enviados:**
- `ChatMessageSent` - Nova mensagem
- `MessageRead` - Mensagem lida
- `UserTyping` - Usuário digitando
- `UserOnlineStatus` - Status online/offline

**Canais:**
- `chat.{roomId}` - Canal privado da sala
- `online-status` - Canal público para status

### API Endpoints

- `GET /chat` - Lista de conversas
- `GET /chat/room/{room}/messages` - Carregar mensagens
- `POST /chat/room/{room}/send` - Enviar mensagem
- `POST /chat/room/{room}/mark-read` - Marcar como lida
- `POST /chat/room/{room}/typing` - Notificar digitação
- `POST /chat/room/{room}/upload` - Upload de arquivo
- `GET /chat/search-users` - Buscar usuários
- `GET /chat/start/{user}` - Iniciar chat privado
- `POST /chat/group/create` - Criar grupo

## 📱 Mobile First

O chat foi desenvolvido com foco em mobile:

- ✅ Layout adaptativo
- ✅ Gestos touch-friendly
- ✅ Teclado virtual otimizado
- ✅ Performance em redes lentas
- ✅ PWA ready (pode virar app)

## 🎨 Personalização

### Cores e Tema

Edite as cores no arquivo de configuração do Tailwind:
- Primary: Cor principal das mensagens enviadas
- Gray/Navy: Cores de fundo e mensagens recebidas

### Avatar dos Usuários

Por padrão usa `ui-avatars.com`. Para usar fotos reais:
1. Adicione campo `avatar` na tabela `users`
2. Atualize métodos `getAvatarUrl()` nos models

## 🐛 Troubleshooting

### WebSocket não conecta

1. Verifique se o Reverb está rodando: `php artisan reverb:start`
2. Confirme as variáveis de ambiente
3. Verifique firewall/portas

### Mensagens não aparecem em tempo real

1. Limpe o cache: `php artisan cache:clear`
2. Reconstrua assets: `npm run build`
3. Verifique console do navegador (F12)

### Erro de autenticação no canal

1. Verifique `routes/channels.php`
2. Confirme CSRF token na página
3. Limpe sessions: `php artisan session:clear`

## 📈 Próximas Melhorias Sugeridas

- [ ] Notificações push no navegador
- [ ] Áudio de notificação
- [ ] Mensagens de voz
- [ ] Videochamadas
- [ ] Compartilhamento de localização
- [ ] Mensagens temporárias (autodestrutivas)
- [ ] Criptografia end-to-end
- [ ] Backup automático de conversas
- [ ] Busca em mensagens antigas
- [ ] Reações com emoji
- [ ] Responder mensagens específicas
- [ ] Encaminhar mensagens

## 🔒 Segurança

- ✅ Autenticação obrigatória
- ✅ Autorização por canal
- ✅ Validação de uploads
- ✅ Proteção CSRF
- ✅ Soft delete de mensagens
- ✅ Sanitização de inputs

## 📄 Licença

Sistema desenvolvido para uso interno.

