<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Mensagens em Massa"
            subtitle="Enviar mensagens para múltiplos usuários ou criar grupos"
            hide-title-mobile
            icon="message-square"
        />
    </x-slot>

    <div x-data="broadcastMessagesApp()" x-init="init()" class="space-y-6">
        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-primary-100 dark:bg-primary-900 rounded-md p-3">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Total de Usuários</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="totalUsers"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-md p-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Secretarias</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="totalSecretariats"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-navy-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-md p-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-navy-300">Selecionados</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white" x-text="selectedUsers.length"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white dark:bg-navy-800 rounded-lg shadow">
            <div class="border-b border-gray-200 dark:border-navy-700">
                <nav class="flex -mb-px">
                    <button
                        @click="activeTab = 'individual'"
                        :class="activeTab === 'individual' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-navy-300 dark:hover:text-white'"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Mensagens Individuais
                    </button>
                    <button
                        @click="activeTab = 'group'"
                        :class="activeTab === 'group' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-navy-300 dark:hover:text-white'"
                        class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Criar Grupo
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tab: Mensagens Individuais -->
                <div x-show="activeTab === 'individual'" x-transition>
                    <form @submit.prevent="sendIndividualMessages" class="space-y-6">
                        <!-- Filtros de Seleção -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Busca por Nome -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                    Buscar Usuário
                                </label>
                                <input
                                    type="text"
                                    x-model="searchQuery"
                                    @input="filterUsers"
                                    placeholder="Digite o nome ou email..."
                                    class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <!-- Filtro por Secretaria -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                    Filtrar por Secretaria
                                </label>
                                <select
                                    x-model="secretariatFilter"
                                    @change="filterUsers"
                                    class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Todas as Secretarias</option>
                                    <template x-for="sec in secretariats" :key="sec.id">
                                        <option :value="sec.id" x-text="sec.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Seleção Rápida -->
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="selectAll"
                                class="px-3 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 rounded-md text-sm font-medium hover:bg-primary-200 dark:hover:bg-primary-900/50 transition">
                                Selecionar Todos
                            </button>
                            <button
                                type="button"
                                @click="deselectAll"
                                class="px-3 py-1.5 bg-gray-100 dark:bg-navy-700 text-gray-700 dark:text-navy-200 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-navy-600 transition">
                                Limpar Seleção
                            </button>
                        </div>

                        <!-- Lista de Usuários -->
                        <div class="border border-gray-300 dark:border-navy-600 rounded-lg overflow-hidden">
                            <div class="max-h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                                    <thead class="bg-gray-50 dark:bg-navy-900 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">
                                                <input
                                                    type="checkbox"
                                                    @change="toggleAllFiltered"
                                                    :checked="allFilteredSelected"
                                                    class="rounded border-gray-300 dark:border-navy-600 text-primary-600 focus:ring-primary-500">
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Nome</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Email</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Cargo</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Secretaria</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                                        <template x-for="user in filteredUsers" :key="user.id">
                                            <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input
                                                        type="checkbox"
                                                        :value="user.id"
                                                        x-model="selectedUsers"
                                                        class="rounded border-gray-300 dark:border-navy-600 text-primary-600 focus:ring-primary-500">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="user.name"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-navy-300" x-text="user.email"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-navy-300" x-text="user.role?.description || 'N/A'"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-navy-300" x-text="user.secretariat?.name || 'N/A'"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="filteredUsers.length === 0">
                                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-navy-300">
                                                Nenhum usuário encontrado
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mensagem -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                Mensagem <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                x-model="individualMessage"
                                rows="5"
                                required
                                placeholder="Digite a mensagem que será enviada para todos os usuários selecionados..."
                                class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                maxlength="5000"></textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                                <span x-text="individualMessage.length"></span> / 5000 caracteres
                            </p>
                        </div>

                        <!-- Botão de Envio -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="selectedUsers.length === 0 || !individualMessage"
                                :class="selectedUsers.length === 0 || !individualMessage ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-700'"
                                class="px-6 py-2.5 text-white rounded-lg font-medium shadow transition">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Enviar para <span x-text="selectedUsers.length"></span> usuário(s)
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Criar Grupo -->
                <div x-show="activeTab === 'group'" x-transition>
                    <form @submit.prevent="createGroup" class="space-y-6">
                        <!-- Nome do Grupo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                Nome do Grupo <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                x-model="groupName"
                                required
                                placeholder="Ex: Equipe de Motoristas"
                                class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                maxlength="255">
                        </div>

                        <!-- Filtros de Seleção -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Busca por Nome -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                    Buscar Usuário
                                </label>
                                <input
                                    type="text"
                                    x-model="searchQueryGroup"
                                    @input="filterUsersGroup"
                                    placeholder="Digite o nome ou email..."
                                    class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <!-- Filtro por Secretaria -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                    Filtrar por Secretaria
                                </label>
                                <select
                                    x-model="secretariatFilterGroup"
                                    @change="filterUsersGroup"
                                    class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Todas as Secretarias</option>
                                    <template x-for="sec in secretariats" :key="sec.id">
                                        <option :value="sec.id" x-text="sec.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Seleção Rápida -->
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="selectAllGroup"
                                class="px-3 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 rounded-md text-sm font-medium hover:bg-primary-200 dark:hover:bg-primary-900/50 transition">
                                Selecionar Todos
                            </button>
                            <button
                                type="button"
                                @click="deselectAllGroup"
                                class="px-3 py-1.5 bg-gray-100 dark:bg-navy-700 text-gray-700 dark:text-navy-200 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-navy-600 transition">
                                Limpar Seleção
                            </button>
                        </div>

                        <!-- Lista de Usuários -->
                        <div class="border border-gray-300 dark:border-navy-600 rounded-lg overflow-hidden">
                            <div class="max-h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-navy-700">
                                    <thead class="bg-gray-50 dark:bg-navy-900 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">
                                                <input
                                                    type="checkbox"
                                                    @change="toggleAllFilteredGroup"
                                                    :checked="allFilteredSelectedGroup"
                                                    class="rounded border-gray-300 dark:border-navy-600 text-primary-600 focus:ring-primary-500">
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Nome</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Email</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Cargo</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-navy-300 uppercase tracking-wider">Secretaria</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-navy-800 divide-y divide-gray-200 dark:divide-navy-700">
                                        <template x-for="user in filteredUsersGroup" :key="user.id">
                                            <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input
                                                        type="checkbox"
                                                        :value="user.id"
                                                        x-model="selectedUsersGroup"
                                                        class="rounded border-gray-300 dark:border-navy-600 text-primary-600 focus:ring-primary-500">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="user.name"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-navy-300" x-text="user.email"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-navy-300" x-text="user.role?.description || 'N/A'"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-navy-300" x-text="user.secretariat?.name || 'N/A'"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="filteredUsersGroup.length === 0">
                                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-navy-300">
                                                Nenhum usuário encontrado
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mensagem Inicial (Opcional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-navy-200 mb-2">
                                Mensagem Inicial (Opcional)
                            </label>
                            <textarea
                                x-model="groupMessage"
                                rows="4"
                                placeholder="Digite uma mensagem inicial para o grupo..."
                                class="w-full rounded-lg border-gray-300 dark:border-navy-600 dark:bg-navy-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                maxlength="5000"></textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-navy-400">
                                <span x-text="groupMessage.length"></span> / 5000 caracteres
                            </p>
                        </div>

                        <!-- Botão de Criação -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="selectedUsersGroup.length === 0 || !groupName"
                                :class="selectedUsersGroup.length === 0 || !groupName ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-700'"
                                class="px-6 py-2.5 text-white rounded-lg font-medium shadow transition">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Criar Grupo com <span x-text="selectedUsersGroup.length"></span> membro(s)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function broadcastMessagesApp() {
            return {
                // Estado
                activeTab: 'individual',
                users: @json($users),
                secretariats: @json($secretariats),
                filteredUsers: [],
                filteredUsersGroup: [],
                selectedUsers: [],
                selectedUsersGroup: [],
                searchQuery: '',
                searchQueryGroup: '',
                secretariatFilter: '',
                secretariatFilterGroup: '',
                individualMessage: '',
                groupName: '',
                groupMessage: '',

                init() {
                    this.filteredUsers = this.users;
                    this.filteredUsersGroup = this.users;
                },

                get totalUsers() {
                    return this.users.length;
                },

                get totalSecretariats() {
                    return this.secretariats.length;
                },

                get allFilteredSelected() {
                    return this.filteredUsers.length > 0 &&
                           this.filteredUsers.every(u => this.selectedUsers.includes(u.id));
                },

                get allFilteredSelectedGroup() {
                    return this.filteredUsersGroup.length > 0 &&
                           this.filteredUsersGroup.every(u => this.selectedUsersGroup.includes(u.id));
                },

                filterUsers() {
                    this.filteredUsers = this.users.filter(user => {
                        const matchesSearch = !this.searchQuery ||
                            user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            user.email.toLowerCase().includes(this.searchQuery.toLowerCase());

                        const matchesSecretariat = !this.secretariatFilter ||
                            user.secretariat_id === this.secretariatFilter;

                        return matchesSearch && matchesSecretariat;
                    });
                },

                filterUsersGroup() {
                    this.filteredUsersGroup = this.users.filter(user => {
                        const matchesSearch = !this.searchQueryGroup ||
                            user.name.toLowerCase().includes(this.searchQueryGroup.toLowerCase()) ||
                            user.email.toLowerCase().includes(this.searchQueryGroup.toLowerCase());

                        const matchesSecretariat = !this.secretariatFilterGroup ||
                            user.secretariat_id === this.secretariatFilterGroup;

                        return matchesSearch && matchesSecretariat;
                    });
                },

                selectAll() {
                    this.selectedUsers = this.users.map(u => u.id);
                },

                deselectAll() {
                    this.selectedUsers = [];
                },

                selectAllGroup() {
                    this.selectedUsersGroup = this.users.map(u => u.id);
                },

                deselectAllGroup() {
                    this.selectedUsersGroup = [];
                },

                toggleAllFiltered() {
                    if (this.allFilteredSelected) {
                        // Desselecionar todos os filtrados
                        this.selectedUsers = this.selectedUsers.filter(
                            id => !this.filteredUsers.some(u => u.id === id)
                        );
                    } else {
                        // Selecionar todos os filtrados
                        const filteredIds = this.filteredUsers.map(u => u.id);
                        this.selectedUsers = [...new Set([...this.selectedUsers, ...filteredIds])];
                    }
                },

                toggleAllFilteredGroup() {
                    if (this.allFilteredSelectedGroup) {
                        // Desselecionar todos os filtrados
                        this.selectedUsersGroup = this.selectedUsersGroup.filter(
                            id => !this.filteredUsersGroup.some(u => u.id === id)
                        );
                    } else {
                        // Selecionar todos os filtrados
                        const filteredIds = this.filteredUsersGroup.map(u => u.id);
                        this.selectedUsersGroup = [...new Set([...this.selectedUsersGroup, ...filteredIds])];
                    }
                },

                async sendIndividualMessages() {
                    if (!confirm(`Deseja realmente enviar esta mensagem para ${this.selectedUsers.length} usuário(s)?`)) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('message', this.individualMessage);
                    this.selectedUsers.forEach(id => formData.append('recipients[]', id));

                    try {
                        const response = await fetch('{{ route("broadcast-messages.send-individual") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: formData
                        });

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Erro ao enviar mensagens');
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        alert('Erro ao enviar mensagens');
                    }
                },

                async createGroup() {
                    if (!confirm(`Deseja realmente criar um grupo com ${this.selectedUsersGroup.length} membro(s)?`)) {
                        return;
                    }

                    const formData = new FormData();
                    formData.append('name', this.groupName);
                    formData.append('message', this.groupMessage);
                    this.selectedUsersGroup.forEach(id => formData.append('participants[]', id));

                    try {
                        const response = await fetch('{{ route("broadcast-messages.create-group") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: formData
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        } else {
                            alert('Erro ao criar grupo');
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        alert('Erro ao criar grupo');
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

