<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Perfil do Usuário" subtitle="Atualize suas informações pessoais" hide-title-mobile icon="user" />
    </x-slot>

    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('dashboard')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <div class="space-y-6">
        <!-- Informações Pessoais -->
        <x-ui.card title="Informações Pessoais">
            <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Foto do Perfil -->
                    <div class="md:col-span-2">
                        <x-input-label for="photo" value="Foto do Perfil" />
                        <div class="flex items-center gap-4 mt-2">
                            <div class="relative">
                                @if(auth()->user()->photo)
                                    <img src="{{ auth()->user()->photo_url }}"
                                         alt="Foto de perfil"
                                         class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file"
                                       id="photo"
                                       name="photo"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                                <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF. Máximo: 5MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Foto da CNH -->
                    <div class="md:col-span-2">
                        <x-input-label for="cnh_photo" value="Foto da CNH" />
                        <div class="flex items-center gap-4 mt-2">
                            <div class="relative">
                                @if(auth()->user()->photoCnh)
                                    <img src="{{ auth()->user()->photo_cnh_url }}"
                                         alt="Foto da CNH"
                                         class="w-20 h-20 rounded object-cover border-2 border-gray-300">
                                @else
                                    <div class="w-20 h-20 rounded bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file"
                                       id="cnh_photo"
                                       name="cnh_photo"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                <x-input-error class="mt-2" :messages="$errors->get('cnh_photo')" />
                                <p class="text-xs text-gray-500 mt-1">Formatos: JPG, PNG, GIF. Máximo: 5MB</p>

                                @if(auth()->user()->photoCnh)
                                    <button type="button"
                                            onclick="event.preventDefault(); document.getElementById('remove-cnh-photo-form').submit();"
                                            class="mt-2 text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        Remover foto da CNH
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Formulário para remover foto da CNH -->
                    @if(auth()->user()->photoCnh)
                        <form id="remove-cnh-photo-form" method="POST" action="{{ route('profile.remove-cnh-photo') }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif

                    <!-- Nome -->
                    <div>
                        <x-input-label for="name" value="Nome Completo *" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email *" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      :value="old('email', $user->email)" required autocomplete="email" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <!-- CPF -->
                    <div>
                        <x-input-label for="cpf" value="CPF *" />
                        <x-text-input id="cpf" name="cpf" type="text" class="mt-1 block w-full cpf-mask"
                                      :value="old('cpf', $user->cpf)" required autocomplete="cpf" />
                        <x-input-error class="mt-2" :messages="$errors->get('cpf')" />
                    </div>

                    <!-- Telefone -->
                    <div>
                        <x-input-label for="phone" value="Telefone" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full phone-mask"
                                      :value="old('phone', $user->phone)" autocomplete="phone" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <!-- Secretaria -->
                    <div>
                        <x-input-label for="secretariat_id" value="Secretaria *" />
                        <select id="secretariat_id" name="secretariat_id" required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                            <option value="">Selecione uma secretaria</option>
                            @foreach($secretariats as $secretariat)
                                <option value="{{ $secretariat->id }}"
                                    {{ old('secretariat_id', $user->secretariat_id) == $secretariat->id ? 'selected' : '' }}>
                                    {{ $secretariat->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('secretariat_id')" />
                    </div>

                    <!-- Status (apenas exibição) -->
                    <div>
                        <x-input-label value="Status" />
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->status === 'active' ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Informações da CNH -->
                <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações da CNH (Opcional)</h3>

                    <div class="grid gap-4 md:grid-cols-3">
                        <!-- Número da CNH -->
                        <div>
                            <x-input-label for="cnh" value="Número da CNH" />
                            <x-text-input id="cnh" name="cnh" type="text" class="mt-1 block w-full"
                                          :value="old('cnh', $user->cnh)" autocomplete="cnh" />
                            <x-input-error class="mt-2" :messages="$errors->get('cnh')" />
                        </div>

                        <!-- Data de Validade -->
                        <div>
                            <x-input-label for="cnh_expiration_date" value="Data de Validade" />
                            <x-text-input id="cnh_expiration_date" name="cnh_expiration_date" type="date"
                                          class="mt-1 block w-full"
                                          :value="old('cnh_expiration_date', $user->cnh_expiration_date)" />
                            <x-input-error class="mt-2" :messages="$errors->get('cnh_expiration_date')" />
                        </div>

                        <!-- Categoria -->
                        <div>
                            <x-input-label for="cnh_category_id" value="Categoria" />
                            <select id="cnh_category_id" name="cnh_category_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                <option value="">Selecione</option>
                                @foreach($cnhCategories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('cnh_category_id', $user->cnh_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->code }} - {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('cnh_category_id')" />
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <x-primary-button icon="save" compact>Salvar Alterações</x-primary-button>

                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                           class="text-sm text-green-600 dark:text-green-400">
                            Alterações salvas com sucesso!
                        </p>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <!-- Alterar Senha -->
        <x-ui.card title="Alterar Senha">
            <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                @method('put')

                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Senha Atual -->
                    <div>
                        <x-input-label for="current_password" value="Senha Atual *" />
                        <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full"
                                      autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <!-- Nova Senha -->
                    <div>
                        <x-input-label for="password" value="Nova Senha *" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                      autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirmar Nova Senha -->
                    <div class="md:col-span-2">
                        <x-input-label for="password_confirmation" value="Confirmar Nova Senha *" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                      class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button icon="key" compact>Alterar Senha</x-primary-button>

                    @if (session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                           class="text-sm text-green-600 dark:text-green-400">
                            Senha alterada com sucesso!
                        </p>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <!-- Excluir Conta -->
        <x-ui.card title="Excluir Conta" class="bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800">
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                <p>Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente apagados. Antes de excluir sua conta, faça o download de quaisquer dados ou informações que deseja manter.</p>
            </div>

            <div class="mt-5">
                <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" icon="trash">
                    Excluir Conta
                </x-danger-button>
            </div>

            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Tem certeza que deseja excluir sua conta?
                    </h2>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente apagados. Por favor, digite sua senha para confirmar que deseja excluir permanentemente sua conta.
                    </p>

                    <div class="mt-6">
                        <x-input-label for="password" value="Senha" class="sr-only" />

                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            class="mt-1 block w-full"
                            placeholder="Digite sua senha"
                        />

                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            Cancelar
                        </x-secondary-button>

                        <x-danger-button>
                            Excluir Conta
                        </x-danger-button>
                    </div>
                </form>
            </x-modal>
        </x-ui.card>
    </div>
    {{-- Incluir o editor de imagem SIMPLES --}}
    @include('components.image-editor-simple')

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Máscaras para os campos
                if (typeof $ !== 'undefined') {
                    $('.cpf-mask').mask('000.000.000-00');
                    $('.phone-mask').mask('(00) 00000-0000');
                }

                // Configurar editor de imagem para foto do perfil e CNH
                const setupImageEditor = () => {
                    const photoInput = document.getElementById('photo');
                    const cnhPhotoInput = document.getElementById('cnh_photo');

                    const handleImageSelect = (input) => {
                        return function(e) {
                            if (e.target.files && e.target.files[0]) {
                                const file = e.target.files[0];

                                // Verifica se é imagem e tamanho (5MB)
                                if (file.type.startsWith('image/')) {
                                    if (file.size <= 5 * 1024 * 1024) {
                                        // Tenta abrir o editor simples
                                        if (typeof window.openSimpleImageEditor === 'function') {
                                            const success = window.openSimpleImageEditor(file, input);
                                            if (!success) {
                                                console.log('Editor não disponível, enviando imagem diretamente');
                                                // Continua com o upload normal
                                            }
                                        } else {
                                            console.log('Função do editor não encontrada, enviando imagem diretamente');
                                            // Continua com o upload normal
                                        }
                                    } else {
                                        alert('A imagem deve ter no máximo 5MB');
                                        input.value = '';
                                    }
                                } else {
                                    alert('Por favor, selecione um arquivo de imagem válido (JPG, PNG, GIF)');
                                    input.value = '';
                                }
                            }
                        };
                    };

                    if (photoInput) {
                        photoInput.addEventListener('change', handleImageSelect(photoInput));
                    }

                    if (cnhPhotoInput) {
                        cnhPhotoInput.addEventListener('change', handleImageSelect(cnhPhotoInput));
                    }
                };

                setupImageEditor();
            });
        </script>
    @endpush
</x-app-layout>
