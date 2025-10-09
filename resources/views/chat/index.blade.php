<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Chat"
            subtitle="Comunicação em tempo real"
            hide-title-mobile
            icon="message-circle"
        />
    </x-slot>

    <div x-data="chatApp()" x-init="init()" class="h-[calc(100vh-8rem)] md:h-[calc(100vh-10rem)]">
        <!-- Container Principal estilo WhatsApp -->
        <div class="h-full bg-white dark:bg-navy-900 rounded-lg shadow-xl overflow-hidden flex">

            <!-- Sidebar - Lista de Conversas -->
            <div
                :class="{'hidden md:flex': activeRoomId, 'flex': !activeRoomId}"
                class="w-full md:w-96 flex-col border-r border-gray-200 dark:border-navy-700">

                <!-- Header da Sidebar -->
                <div class="bg-primary-600 dark:bg-primary-700 px-4 py-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Conversas</h2>
                    <button
                        @click="showNewChatModal = true"
                        class="p-2 hover:bg-primary-700 dark:hover:bg-primary-800 rounded-full transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>

                <!-- Busca -->
                <div class="p-3 bg-gray-50 dark:bg-navy-800 border-b border-gray-200 dark:border-navy-700">
                    <div class="relative">
                        <input
                            type="text"
                            x-model="searchQuery"
                            @input="filterChats"
                            placeholder="Buscar conversas..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
                        <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Lista de Conversas -->
                <div class="flex-1 overflow-y-auto">
                    <template x-for="room in filteredRooms" :key="room.id">
                        <div
                            @click="selectRoom(room.id)"
                            :class="{'bg-primary-50 dark:bg-primary-900/20 border-l-4 border-primary-600': activeRoomId === room.id}"
                            class="px-4 py-3 border-b border-gray-100 dark:border-navy-800 hover:bg-gray-50 dark:hover:bg-navy-800 cursor-pointer transition">
                            <div class="flex items-center gap-3">
                                <!-- Avatar -->
                                <div class="relative">
                                    <img :src="room.avatar_url" :alt="room.display_name" class="w-12 h-12 rounded-full">
                                    <span
                                        x-show="room.other_user && isUserOnline(room.other_user.id)"
                                        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-navy-900 rounded-full"></span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="font-semibold text-gray-900 dark:text-white truncate" x-text="room.display_name"></h3>
                                        <span
                                            x-show="room.latest_message"
                                            x-text="room.latest_message ? formatTime(room.latest_message.created_at) : ''"
                                            class="text-xs text-gray-500 dark:text-navy-400"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <p
                                            class="text-sm text-gray-600 dark:text-navy-300 truncate"
                                            x-text="room.latest_message ? (room.latest_message.message || '📎 Anexo') : 'Sem mensagens'"></p>
                                        <span
                                            x-show="room.unread_count > 0"
                                            x-text="room.unread_count"
                                            class="ml-2 px-2 py-0.5 bg-primary-600 text-white text-xs font-semibold rounded-full"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Estado Vazio -->
                    <div x-show="rooms.length === 0" class="p-8 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-navy-400">Nenhuma conversa ainda</p>
                        <button
                            @click="showNewChatModal = true"
                            class="mt-4 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm transition">
                            Iniciar Chat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Área de Chat -->
            <div
                :class="{'hidden md:flex': !activeRoomId, 'flex': activeRoomId}"
                class="flex-1 flex flex-col">

                <template x-if="activeRoomId">
                    <div class="flex flex-col h-full">
                        <!-- Header do Chat -->
                        <div class="bg-gray-50 dark:bg-navy-800 px-4 py-3 border-b border-gray-200 dark:border-navy-700 flex items-center gap-3">
                            <!-- Botão Voltar (Mobile) -->
                            <button
                                @click="activeRoomId = null"
                                class="md:hidden p-2 hover:bg-gray-200 dark:hover:bg-navy-700 rounded-full transition">
                                <svg class="w-5 h-5 text-gray-600 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            <div class="relative">
                                <img :src="activeRoom?.avatar_url" :alt="activeRoom?.display_name" class="w-10 h-10 rounded-full">
                                <span
                                    x-show="activeRoom?.other_user && isUserOnline(activeRoom.other_user.id)"
                                    class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-navy-900 rounded-full"></span>
                            </div>

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="activeRoom?.display_name"></h3>
                                <p class="text-xs text-gray-500 dark:text-navy-400">
                                    <span x-show="typingUsers.length > 0" x-text="getTypingText()"></span>
                                    <span x-show="typingUsers.length === 0 && activeRoom?.other_user">
                                        <span x-show="activeRoom?.other_user && isUserOnline(activeRoom.other_user.id)" class="text-green-600 dark:text-green-400">● Online</span>
                                        <span x-show="activeRoom?.other_user && !isUserOnline(activeRoom.other_user.id)" x-text="activeRoom?.other_user ? getLastSeenText(activeRoom.other_user.id) : ''"></span>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Mensagens -->
                        <div
                            x-ref="messagesContainer"
                            class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-100 dark:bg-navy-900"
                            style="background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0iYSIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVHJhbnNmb3JtPSJyb3RhdGUoNDUpIj48cGF0aCBkPSJNLTEwIDMwaDYwdjJoLTYweiIgZmlsbD0icmdiYSgwLDAsMCwwLjAyKSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNhKSIvPjwvc3ZnPg==');">

                            <template x-for="(message, index) in messages" :key="message.id">
                                <div>
                                    <!-- Separador de Data -->
                                    <div
                                        x-show="shouldShowDateSeparator(index)"
                                        class="flex justify-center my-4">
                                        <span class="bg-white dark:bg-navy-800 px-4 py-1 rounded-full text-xs text-gray-600 dark:text-navy-300 shadow-sm"
                                            x-text="message.formatted_date"></span>
                                    </div>

                                    <!-- Mensagem -->
                                    <div
                                        :class="message.user_id === currentUserId ? 'justify-end' : 'justify-start'"
                                        class="flex gap-2">

                                        <!-- Mensagem do Outro Usuário -->
                                        <div
                                            x-show="message.user_id !== currentUserId"
                                            :class="message.user_id === currentUserId ? 'bg-primary-600 text-white' : 'bg-white dark:bg-navy-800 text-gray-900 dark:text-white'"
                                            class="max-w-[75%] rounded-lg px-4 py-2 shadow-sm">

                                            <!-- Nome do Usuário (em grupos) -->
                                            <div x-show="activeRoom?.type === 'group'" class="text-xs font-semibold mb-1 text-primary-600 dark:text-primary-400" x-text="message.user.name"></div>

                                            <!-- Imagem -->
                                            <div x-show="message.is_image" class="mb-2">
                                                <img :src="message.attachment_url" :alt="message.message" class="rounded-lg max-w-full h-auto cursor-pointer" @click="openImageModal(message.attachment_url)">
                                            </div>

                                            <!-- Arquivo -->
                                            <div x-show="message.is_file" class="flex items-center gap-2 p-2 bg-gray-100 dark:bg-navy-700 rounded mb-2">
                                                <svg class="w-6 h-6 text-gray-600 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <a :href="message.attachment_url" target="_blank" class="text-sm hover:underline">
                                                    <span x-text="'Arquivo anexado'"></span>
                                                </a>
                                            </div>

                                            <!-- Texto -->
                                            <p x-show="message.message" class="text-sm whitespace-pre-wrap break-words" x-text="message.message"></p>

                                            <div class="flex items-center justify-end gap-1 mt-1">
                                                <span class="text-xs opacity-70" x-text="message.formatted_time"></span>
                                            </div>
                                        </div>

                                        <!-- Mensagem do Usuário Atual -->
                                        <div
                                            x-show="message.user_id === currentUserId"
                                            class="max-w-[75%] rounded-lg px-4 py-2 shadow-sm bg-primary-600 text-white">

                                            <!-- Imagem -->
                                            <div x-show="message.is_image" class="mb-2">
                                                <img :src="message.attachment_url" :alt="message.message" class="rounded-lg max-w-full h-auto cursor-pointer" @click="openImageModal(message.attachment_url)">
                                            </div>

                                            <!-- Arquivo -->
                                            <div x-show="message.is_file" class="flex items-center gap-2 p-2 bg-primary-700 rounded mb-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <a :href="message.attachment_url" target="_blank" class="text-sm hover:underline">Arquivo anexado</a>
                                            </div>

                                            <!-- Texto -->
                                            <p x-show="message.message" class="text-sm whitespace-pre-wrap break-words" x-text="message.message"></p>

                                            <div class="flex items-center justify-end gap-1 mt-1">
                                                <span class="text-xs opacity-90" x-text="message.formatted_time"></span>
                                                <!-- Check de Leitura -->
                                                <svg x-show="message.read_status === 'read'" class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7M5 13l4 4L19 7"/>
                                                </svg>
                                                <svg x-show="message.read_status === 'delivered'" class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <svg x-show="message.read_status === 'sent'" class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Input de Mensagem -->
                        <div class="bg-gray-50 dark:bg-navy-800 px-4 py-3 border-t border-gray-200 dark:border-navy-700">
                            <div class="flex items-end gap-2">
                                <!-- Upload de Arquivo -->
                                <input
                                    type="file"
                                    x-ref="fileInput"
                                    @change="handleFileUpload"
                                    class="hidden"
                                    accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">

                                <button
                                    type="button"
                                    @click="$refs.fileInput.click()"
                                    class="p-2 text-gray-600 dark:text-navy-300 hover:bg-gray-200 dark:hover:bg-navy-700 rounded-full transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                </button>

                                <!-- Textarea -->
                                <div class="flex-1 relative">
                                    <textarea
                                        x-model="newMessage"
                                        @input="handleTyping"
                                        @keydown.enter.exact.prevent="sendMessage"
                                        rows="1"
                                        placeholder="Digite uma mensagem..."
                                        class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white resize-none focus:ring-primary-500 focus:border-primary-500"
                                        style="max-height: 120px;"></textarea>
                                </div>

                                <!-- Botão Enviar -->
                                <button
                                    type="button"
                                    @click="sendMessage"
                                    :disabled="!newMessage.trim() && !uploadedFile"
                                    :class="newMessage.trim() || uploadedFile ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-400'"
                                    class="p-3 text-white rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Preview de Arquivo -->
                            <div x-show="uploadedFile" class="mt-2 flex items-center gap-2 p-2 bg-primary-100 dark:bg-primary-900/20 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="uploadedFile?.name"></span>
                                <button @click="uploadedFile = null" class="ml-auto text-red-600 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Estado sem chat selecionado -->
                <template x-if="!activeRoomId">
                    <div class="flex-1 hidden md:flex items-center justify-center bg-gray-50 dark:bg-navy-900">
                        <div class="text-center">
                            <!-- Placeholder user icon -->
                            <div class="w-24 h-24 bg-gray-200 dark:bg-navy-700 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400 dark:text-navy-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 dark:text-navy-300 mb-2">Selecione uma conversa</h3>
                            <p class="text-gray-500 dark:text-navy-400">Escolha uma conversa existente ou inicie uma nova</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal - Nova Conversa -->
        <div
            x-show="showNewChatModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="showNewChatModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showNewChatModal = false"></div>

                <div class="relative bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-md w-full">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-navy-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nova Conversa</h3>
                    </div>

                    <div class="p-6">
                        <input
                            type="text"
                            x-model="userSearchQuery"
                            @input="searchUsers"
                            placeholder="Buscar usuários..."
                            class="w-full mb-4 rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">

                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            <template x-for="user in searchedUsers" :key="user.id">
                                <div
                                    @click="startChatWithUser(user.id)"
                                    class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-navy-700 rounded-lg cursor-pointer transition">
                                    <img :src="user.avatar_url" :alt="user.name" class="w-10 h-10 rounded-full">
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white" x-text="user.name"></h4>
                                        <p class="text-sm text-gray-500 dark:text-navy-400" x-text="user.email"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal - Visualizar Imagem -->
        <div
            x-show="imageModalUrl"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
            @click="imageModalUrl = null">
            <img :src="imageModalUrl" alt="Imagem" class="max-w-full max-h-full object-contain">
            <button
                @click="imageModalUrl = null"
                class="absolute top-4 right-4 text-white hover:text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function chatApp() {
            return {
                // Estado
                currentUserId: '{{ auth()->id() }}',
                rooms: @json($chatRooms->values()),
                activeRoomId: @json($activeRoom->id ?? null),
                activeRoom: @json($activeRoom ?? null),
                messages: [],
                newMessage: '',
                searchQuery: '',
                filteredRooms: [],
                showNewChatModal: false,
                userSearchQuery: '',
                searchedUsers: [],
                typingUsers: [],
                onlineUsers: [],
                uploadedFile: null,
                imageModalUrl: null,
                typingTimeout: null,
                echo: null,
                currentChannel: null,
                isLoadingMessages: false,

                init() {
                    console.log('Chat App iniciando...', {
                        currentUserId: this.currentUserId,
                        roomsCount: this.rooms.length,
                        activeRoomId: this.activeRoomId
                    });

                    this.filteredRooms = this.rooms;

                    if (this.activeRoomId) {
                        this.loadMessages();
                    }

                    this.initWebSocket();
                    this.listenToOnlineStatus();

                    // Desabilitar page loading COMPLETAMENTE para a página de chat
                    this.disablePageLoadingForChat();
                },

                disablePageLoadingForChat() {
                    // Desabilitar o page loading imediatamente
                    const setLoadingOff = () => {
                        const layoutData = Alpine.$data(document.documentElement);
                        if (layoutData && layoutData.pageLoading !== undefined) {
                            layoutData.pageLoading = false;
                        }
                    };

                    setLoadingOff();

                    // Monitorar mudanças no pageLoading e forçar para false
                    setInterval(setLoadingOff, 100);

                    // Interceptar todos os eventos que ativam o loading
                    document.addEventListener('submit', (e) => {
                        // Se for o formulário do chat, prevenir comportamento padrão
                        const form = e.target;
                        if (form && form.closest('[x-data*="chatApp"]')) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                        }
                        setLoadingOff();
                        return false;
                    }, true);

                    document.addEventListener('click', (e) => {
                        setLoadingOff();
                    }, true);

                    // Sobrescrever o fetch para garantir que o loading nunca seja ativado
                    const originalFetch = window.fetch;
                    window.fetch = (...args) => {
                        setLoadingOff();
                        setTimeout(setLoadingOff, 0);
                        setTimeout(setLoadingOff, 50);
                        setTimeout(setLoadingOff, 100);
                        return originalFetch.apply(window, args);
                    };
                },

                initWebSocket() {
                    // Inicializar Laravel Echo com Reverb
                    this.echo = window.Echo;

                    // Se há sala ativa, conectar ao canal
                    if (this.activeRoomId) {
                        this.subscribeToRoom(this.activeRoomId);
                    }
                },

                subscribeToRoom(roomId) {
                    console.log('Inscrevendo no canal:', roomId);

                    // Deixar canal anterior
                    if (this.currentChannel) {
                        window.Echo.leave(`chat.${this.currentChannel}`);
                    }

                    this.currentChannel = roomId;

                    // Conectar ao canal privado da sala - USAR OS NOMES CORRETOS DOS EVENTOS
                    window.Echo.private(`chat.${roomId}`)
                        .listen('.message.sent', (e) => {
                            console.log('Nova mensagem recebida via WebSocket:', e);

                            // Verificar se a mensagem já existe (evitar duplicatas)
                            const exists = this.messages.some(m => m.id === e.message.id);
                            if (!exists) {
                                this.messages.push(e.message);
                                this.$nextTick(() => this.scrollToBottom());

                                // Só marcar como lida se não for do usuário atual
                                if (e.message.user_id !== this.currentUserId) {
                                    this.markMessagesAsRead();
                                }

                                // Atualizar a sala na lista
                                this.updateRoomLastMessage(roomId, e.message);
                            }
                        })
                        .listen('.message.read', (e) => {
                            console.log('Mensagem lida:', e);
                            this.updateMessageReadStatus(e.messageId, e.userId);
                        })
                        .listen('.user.typing', (e) => {
                            console.log('Usuário digitando:', e);
                            this.handleUserTyping(e);
                        });
                },

                listenToOnlineStatus() {
                    window.Echo.channel('online-status')
                        .listen('.user.status', (e) => {
                            console.log('Status do usuário:', e);
                            if (e.isOnline) {
                                if (!this.onlineUsers.includes(e.userId)) {
                                    this.onlineUsers.push(e.userId);
                                }
                            } else {
                                this.onlineUsers = this.onlineUsers.filter(id => id !== e.userId);
                            }
                        });
                },

                async selectRoom(roomId) {
                    console.log('Selecionando sala:', roomId);
                    this.activeRoomId = roomId;
                    this.activeRoom = this.rooms.find(r => r.id === roomId);

                    // Atualizar URL sem recarregar
                    window.history.pushState({}, '', `/chat?room=${roomId}`);

                    await this.loadMessages();
                    this.subscribeToRoom(roomId);
                    this.markMessagesAsRead();
                },

                async loadMessages() {
                    if (this.isLoadingMessages) return;

                    this.isLoadingMessages = true;
                    try {
                        const response = await fetch(`/chat/room/${this.activeRoomId}/messages`);
                        if (response.ok) {
                            this.messages = await response.json();
                            console.log('Mensagens carregadas:', this.messages.length);
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (error) {
                        console.error('Erro ao carregar mensagens:', error);
                    } finally {
                        this.isLoadingMessages = false;
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() && !this.uploadedFile) return;

                    // IMPORTANTE: Prevenir qualquer comportamento padrão
                    const layoutData = Alpine.$data(document.documentElement);
                    if (layoutData) {
                        layoutData.pageLoading = false;
                    }

                    const tempMessage = this.newMessage;
                    const tempFile = this.uploadedFile;

                    // Limpar input imediatamente para melhor UX
                    this.newMessage = '';
                    this.uploadedFile = null;

                    const formData = new FormData();
                    formData.append('message', tempMessage);

                    if (tempFile) {
                        formData.append('attachment', tempFile);
                    }

                    try {
                        const response = await fetch(`/chat/room/${this.activeRoomId}/send`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                // Removing Content-Type header - browser will set the correct multipart/form-data with boundary
                            },
                            body: formData
                        });

                        if (response.ok) {
                            let message;
                            try {
                                message = await response.json();
                            } catch (jsonError) {
                                console.error('Erro ao processar resposta:', jsonError);
                                throw new Error('Resposta inválida do servidor');
                            }
                            console.log('Mensagem enviada com sucesso:', message);

                            // Adicionar a mensagem localmente (será confirmada via WebSocket)
                            const exists = this.messages.some(m => m.id === message.id);
                            if (!exists) {
                                // Garantir que a mensagem tenha o formato de data e hora imediatamente
                                if (!message.formatted_time) {
                                    message.formatted_time = this.formatTime(message.created_at);
                                }
                                if (!message.formatted_date) {
                                    message.formatted_date = new Date(message.created_at).toLocaleDateString('pt-BR', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                    });
                                }

                                this.messages.push(message);
                                this.$nextTick(() => this.scrollToBottom());
                            }

                            // Atualizar última mensagem na lista
                            this.updateRoomLastMessage(this.activeRoomId, message);

                            // Garantir que o loading está desabilitado
                            if (layoutData) {
                                layoutData.pageLoading = false;
                            }
                        } else {
                            console.error('Erro ao enviar mensagem:', response.status);
                            // Restaurar mensagem em caso de erro
                            this.newMessage = tempMessage;
                            this.uploadedFile = tempFile;
                            alert('Erro ao enviar mensagem. Tente novamente.');
                        }
                    } catch (error) {
                        console.error('Erro ao enviar mensagem:', error);
                        this.newMessage = tempMessage;
                        this.uploadedFile = tempFile;
                        alert('Erro ao enviar mensagem. Verifique sua conexão.');
                    } finally {
                        // Garantir que o loading está desabilitado
                        if (layoutData) {
                            layoutData.pageLoading = false;
                        }
                    }

                    // Prevenir propagação
                    return false;
                },

                updateRoomLastMessage(roomId, message) {
                    const room = this.rooms.find(r => r.id === roomId);
                    if (room) {
                        room.latest_message = message;
                        room.updated_at = message.created_at;

                        // Reordenar salas por última mensagem
                        this.rooms.sort((a, b) => {
                            const dateA = a.latest_message ? new Date(a.latest_message.created_at) : new Date(a.updated_at);
                            const dateB = b.latest_message ? new Date(b.latest_message.created_at) : new Date(b.updated_at);
                            return dateB - dateA;
                        });

                        this.filterChats();
                    }
                },

                async markMessagesAsRead() {
                    if (!this.activeRoomId) return;

                    const unreadMessageIds = this.messages
                        .filter(m => m.user_id !== this.currentUserId && !m.read_receipts.some(r => r.user_id === this.currentUserId))
                        .map(m => m.id);

                    if (unreadMessageIds.length === 0) return;

                    try {
                        await fetch(`/chat/room/${this.activeRoomId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ message_ids: unreadMessageIds })
                        });

                        // Atualizar contagem de não lidas
                        const room = this.rooms.find(r => r.id === this.activeRoomId);
                        if (room) {
                            room.unread_count = 0;
                        }
                    } catch (error) {
                        console.error('Erro ao marcar como lida:', error);
                    }
                },

                handleTyping() {
                    if (!this.activeRoomId) return;

                    if (this.typingTimeout) {
                        clearTimeout(this.typingTimeout);
                    }

                    fetch(`/chat/room/${this.activeRoomId}/typing`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ typing: true })
                    }).catch(err => console.error('Erro ao enviar typing:', err));

                    this.typingTimeout = setTimeout(() => {
                        fetch(`/chat/room/${this.activeRoomId}/typing`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ typing: false })
                        }).catch(err => console.error('Erro ao enviar typing:', err));
                    }, 3000);
                },

                handleUserTyping(data) {
                    if (data.userId === this.currentUserId) return;

                    if (data.isTyping) {
                        if (!this.typingUsers.includes(data.userName)) {
                            this.typingUsers.push(data.userName);
                        }

                        setTimeout(() => {
                            this.typingUsers = this.typingUsers.filter(u => u !== data.userName);
                        }, 4000);
                    } else {
                        this.typingUsers = this.typingUsers.filter(u => u !== data.userName);
                    }
                },

                async handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (file.size > 10 * 1024 * 1024) {
                        alert('Arquivo muito grande! Máximo 10MB.');
                        event.target.value = '';
                        return;
                    }

                    this.uploadedFile = file;
                },

                async searchUsers() {
                    if (this.userSearchQuery.length < 2) {
                        this.searchedUsers = [];
                        return;
                    }

                    try {
                        const response = await fetch(`/chat/search-users?q=${encodeURIComponent(this.userSearchQuery)}`);
                        if (response.ok) {
                            this.searchedUsers = await response.json();
                            console.log('Usuários encontrados:', this.searchedUsers);
                        } else {
                            console.error('Erro ao buscar usuários:', response.status);
                            this.searchedUsers = [];
                        }
                    } catch (error) {
                        console.error('Erro ao buscar usuários:', error);
                        this.searchedUsers = [];
                    }
                },

                async startChatWithUser(userId) {
                    console.log('Iniciando chat com usuário:', userId);
                    // Desabilitar loading antes de navegar
                    const layoutData = Alpine.$data(document.documentElement);
                    if (layoutData) {
                        layoutData.pageLoading = false;
                    }
                    window.location.href = `/chat/start/${userId}`;
                },

                filterChats() {
                    if (!this.searchQuery) {
                        this.filteredRooms = this.rooms;
                        return;
                    }

                    const query = this.searchQuery.toLowerCase();
                    this.filteredRooms = this.rooms.filter(room => {
                        return room.display_name.toLowerCase().includes(query);
                    });
                },

                updateMessageReadStatus(messageId, userId) {
                    const message = this.messages.find(m => m.id === messageId);
                    if (message) {
                        if (!message.read_receipts.some(r => r.user_id === userId)) {
                            message.read_receipts.push({ user_id: userId, read_at: new Date().toISOString() });
                        }
                    }
                },

                isUserOnline(userId) {
                    return this.onlineUsers.includes(userId);
                },

                getTypingText() {
                    if (this.typingUsers.length === 1) {
                        return `${this.typingUsers[0]} está digitando...`;
                    } else if (this.typingUsers.length > 1) {
                        return 'Várias pessoas estão digitando...';
                    }
                    return '';
                },

                getLastSeenText(userId) {
                    // Implementar lógica de "visto por último"
                    return 'offline';
                },

                shouldShowDateSeparator(index) {
                    if (index === 0) return true;

                    const currentDate = this.messages[index].formatted_date;
                    const previousDate = this.messages[index - 1].formatted_date;

                    return currentDate !== previousDate;
                },

                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                },

                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                        }, 100);
                    }
                },

                openImageModal(url) {
                    this.imageModalUrl = url;
                },

                // Funcionalidade: Copiar mensagem
                copyMessage(message) {
                    navigator.clipboard.writeText(message.message).then(() => {
                        console.log('Mensagem copiada!');
                    });
                },

                // Funcionalidade: Deletar mensagem (apenas suas próprias)
                async deleteMessage(messageId) {
                    if (!confirm('Deseja realmente deletar esta mensagem?')) return;

                    try {
                        const response = await fetch(`/chat/room/${this.activeRoomId}/message/${messageId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        });

                        if (response.ok) {
                            this.messages = this.messages.filter(m => m.id !== messageId);
                        }
                    } catch (error) {
                        console.error('Erro ao deletar mensagem:', error);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
