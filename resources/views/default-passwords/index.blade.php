<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Senhas Padrão" subtitle="Gerenciar senhas padrão do sistema" hide-title-mobile icon="key" />
    </x-slot>
    <x-slot name="pageActions">
        <button @click="$dispatch('toggle-password-form')" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium shadow transition">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Adicionar</span>
        </button>
    </x-slot>

    <div x-data="{ showForm: false, editingId: null }" @toggle-password-form.window="showForm = !showForm" class="space-y-6">
        <!-- Formulário de Criação/Edição -->
        <div x-show="showForm || editingId"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4">
            <x-ui.card>
                <form method="POST" :action="editingId ? '{{ url('default-passwords') }}/' + editingId : '{{ route('default-passwords.store') }}'" class="space-y-6">
                    @csrf
                    <template x-if="editingId">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editingId ? 'Editar Senha Padrão' : 'Nova Senha Padrão'"></h3>
                            <button type="button" @click="showForm = false; editingId = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <x-icon name="close" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Nome -->
                            <div>
                                <x-input-label for="name" value="Nome Identificador *" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required placeholder="ex: reset_password, driver_default" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nome único para identificar esta senha (sem espaços)</p>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Descrição -->
                            <div>
                                <x-input-label for="description" value="Descrição" />
                                <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Senha -->
                            <div>
                                <x-input-label for="password" value="Senha *" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" x-bind:required="!editingId" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="editingId ? 'Deixe em branco para manter a senha atual' : 'Senha que será atribuída aos usuários (mínimo 8 caracteres)'"></p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Senha ativa (disponível para uso)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-primary-button>
                            <x-icon name="save" class="w-4 h-4 mr-2" />
                            <span x-text="editingId ? 'Salvar Alterações' : 'Criar Senha Padrão'"></span>
                        </x-primary-button>

                        <button type="button" @click="showForm = false; editingId = null" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Cancelar
                        </button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <!-- Tabela de Listagem -->
        <x-ui.card>
            <x-ui.table
                :headers="['Nome','Descrição','Status','Ações']"
                :searchable="true"
                search-placeholder="Pesquisar por nome ou descrição..."
                :search-value="$search ?? ''"
                :pagination="$passwords">
                @forelse($passwords as $password)
                    <tr class="hover:bg-gray-50 dark:hover:bg-navy-700/40">
                        <td class="px-4 py-2 font-medium">{{ $password->name }}</td>
                        <td class="px-4 py-2">{{ $password->description ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if($password->is_active)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Ativa
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                    Inativa
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('default-passwords.edit', $password) }}"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/40 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 transition"
                                   title="Editar">
                                    <x-icon name="edit" class="w-4 h-4" />
                                </a>

                                <x-ui.confirm-form
                                    :action="route('default-passwords.destroy', $password)"
                                    method="DELETE"
                                    message="Tem certeza que deseja excluir esta senha padrão? Esta ação não pode ser desfeita."
                                    title="Excluir Senha Padrão"
                                    icon="trash"
                                    variant="danger">
                                    Excluir
                                </x-ui.confirm-form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-navy-200">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <x-icon name="key" class="w-12 h-12 text-gray-400" />
                                <p>Nenhuma senha padrão cadastrada.</p>
                                <button @click="showForm = true" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium text-sm">
                                    Clique aqui para criar a primeira
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
    </div>

    @if($errors->any())
    <script>
        document.addEventListener('alpine:init', () => {
            // Se houver erros de validação, manter o formulário aberto
            window.dispatchEvent(new CustomEvent('show-password-form'));
        });
    </script>
    @endif
</x-app-layout>
