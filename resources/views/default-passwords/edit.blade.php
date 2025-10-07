<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Senha Padrão" subtitle="Atualizar informações da senha padrão" hide-title-mobile icon="key" />
    </x-slot>

    <x-ui.card>
        <form method="POST" action="{{ route('default-passwords.update', $defaultPassword) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informações da Senha</h3>

                <div class="grid grid-cols-1 gap-4">
                    <!-- Nome -->
                    <div>
                        <x-input-label for="name" value="Nome Identificador *" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $defaultPassword->name)" required autofocus />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nome único para identificar esta senha (sem espaços)</p>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Descrição -->
                    <div>
                        <x-input-label for="description" value="Descrição" />
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">{{ old('description', $defaultPassword->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- Botão para Alterar Senha -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <button type="button" id="togglePasswordBtn" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <x-icon name="key" class="w-4 h-4 mr-2" />
                            Alterar Senha
                        </button>
                    </div>

                    <!-- Campo de Senha (oculto por padrão) -->
                    <div id="passwordField" class="hidden">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <x-icon name="alert-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-500 mr-2 flex-shrink-0 mt-0.5" />
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Atenção: Alterar a senha afetará todos os usuários que utilizam esta senha padrão.
                                </p>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="password" value="Nova Senha" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Digite a nova senha (mínimo 8 caracteres). Deixe em branco para manter a senha atual.</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $defaultPassword->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Senha ativa (disponível para uso)</label>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-primary-button>
                    <x-icon name="save" class="w-4 h-4 mr-2" />
                    Salvar Alterações
                </x-primary-button>

                <a href="{{ route('default-passwords.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Cancelar
                </a>
            </div>
        </form>
    </x-ui.card>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordField = document.getElementById('passwordField');
            const passwordInput = document.getElementById('password');

            toggleBtn.addEventListener('click', function() {
                if (passwordField.classList.contains('hidden')) {
                    // Mostrar campo de senha
                    passwordField.classList.remove('hidden');
                    toggleBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Cancelar Alteração de Senha';
                    toggleBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700', 'focus:bg-primary-700', 'active:bg-primary-900');
                    toggleBtn.classList.add('bg-gray-600', 'hover:bg-gray-700', 'focus:bg-gray-700', 'active:bg-gray-900');
                    passwordInput.focus();
                } else {
                    // Ocultar campo de senha
                    passwordField.classList.add('hidden');
                    toggleBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>Alterar Senha';
                    toggleBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700', 'focus:bg-gray-700', 'active:bg-gray-900');
                    toggleBtn.classList.add('bg-primary-600', 'hover:bg-primary-700', 'focus:bg-primary-700', 'active:bg-primary-900');
                    passwordInput.value = '';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
