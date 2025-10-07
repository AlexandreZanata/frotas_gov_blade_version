@csrf
<div class="grid gap-4 md:grid-cols-2">
    <!-- Nome -->
    <div>
        <x-input-label for="name" value="Nome Completo *" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <!-- CPF -->
    <div>
        <x-input-label for="cpf" value="CPF *" />
        <x-input-cpf id="cpf" name="cpf" :value="old('cpf', $user->cpf ?? '')" class="mt-1 block w-full" required />
        <x-input-error :messages="$errors->get('cpf')" class="mt-1" />
    </div>

    <!-- Email -->
    <div>
        <x-input-label for="email" value="E-mail *" />
        <x-input-email id="email" name="email" :value="old('email', $user->email ?? '')" class="mt-1 block w-full" required />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>

    <!-- Telefone -->
    <div>
        <x-input-label for="phone" value="Telefone" />
        <x-input-phone id="phone" name="phone" :value="old('phone', $user->phone ?? '')" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
    </div>

    <!-- Número da CNH -->
    <div>
        <x-input-label for="cnh" value="Número da CNH" />
        <x-input-cnh id="cnh" name="cnh" :value="old('cnh', $user->cnh ?? '')" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('cnh')" class="mt-1" />
    </div>

    <!-- Data de Validade CNH -->
    <div>
        <x-input-label for="cnh_expiration_date" value="Data de Validade CNH" />
        <x-input-date-validated id="cnh_expiration_date" name="cnh_expiration_date" :value="old('cnh_expiration_date', $user->cnh_expiration_date ?? '')" class="mt-1 block w-full" min-date="today" />
        <x-input-error :messages="$errors->get('cnh_expiration_date')" class="mt-1" />
    </div>

    <!-- Categoria CNH -->
    <div>
        <x-input-label for="cnh_category" value="Categoria CNH" />
        <x-ui.select name="cnh_category" id="cnh_category" class="mt-1">
            <option value="">Selecione...</option>
            @foreach(['A', 'B', 'C', 'D', 'E', 'AB', 'AC', 'AD', 'AE'] as $category)
                <option value="{{ $category }}" @selected(old('cnh_category', $user->cnh_category ?? '') == $category)>{{ $category }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('cnh_category')" class="mt-1" />
    </div>

    <!-- Role -->
    <div>
        <x-input-label for="role_id" value="Função (Role) *" />
        <x-ui.select name="role_id" id="role_id" class="mt-1" required>
            <option value="">Selecione...</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id ?? '') == $role->id)>{{ $role->display_name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('role_id')" class="mt-1" />
    </div>

    <!-- Secretaria -->
    <div>
        <x-input-label for="secretariat_id" value="Secretaria *" />
        <x-ui.select name="secretariat_id" id="secretariat_id" class="mt-1" required>
            <option value="">Selecione...</option>
            @foreach($secretariats as $secretariat)
                <option value="{{ $secretariat->id }}" @selected(old('secretariat_id', $user->secretariat_id ?? '') == $secretariat->id)>{{ $secretariat->name }}</option>
            @endforeach
        </x-ui.select>
        <x-input-error :messages="$errors->get('secretariat_id')" class="mt-1" />
    </div>

    <!-- Status (apenas para edição) -->
    @if(isset($user) && $user->exists)
    <div>
        <x-input-label for="status" value="Status *" />
        <x-ui.select name="status" id="status" class="mt-1" required>
            <option value="active" @selected(old('status', $user->status ?? 'active') == 'active')>Ativo</option>
            <option value="inactive" @selected(old('status', $user->status ?? 'active') == 'inactive')>Inativo</option>
        </x-ui.select>
        <x-input-error :messages="$errors->get('status')" class="mt-1" />
    </div>
    @endif
</div>

<!-- Seção de Senha -->
@if(!isset($user) || !$user->exists)
    <!-- Formulário de Criação: Senha obrigatória -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Senha de Acesso</h3>

        <div x-data="{ useDefaultPassword: true }">
            <!-- Escolher tipo de senha -->
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="radio" x-model="useDefaultPassword" :value="true" name="use_default_password" value="1" class="form-radio text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600" checked>
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Usar senha padrão</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="radio" x-model="useDefaultPassword" :value="false" name="use_default_password" value="0" class="form-radio text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Criar senha personalizada</span>
                </label>
            </div>

            <!-- Campo de senha padrão -->
            <div x-show="useDefaultPassword">
                <x-input-label for="default_password_id" value="Senha Padrão *" />
                <x-ui.select name="default_password_id" id="default_password_id" class="mt-1" x-bind:required="useDefaultPassword">
                    <option value="">Selecione uma senha padrão...</option>
                    @foreach($defaultPasswords as $dp)
                        <option value="{{ $dp->id }}" @selected(old('default_password_id') == $dp->id)>
                            {{ $dp->name }}{{ $dp->description ? ' - ' . $dp->description : '' }}
                        </option>
                    @endforeach
                </x-ui.select>
                <x-input-error :messages="$errors->get('default_password_id')" class="mt-1" />
            </div>

            <!-- Campos de senha personalizada -->
            <div x-show="!useDefaultPassword">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="password" value="Senha *" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" x-bind:required="!useDefaultPassword" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar Senha *" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Formulário de Edição: Senha opcional -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Alterar Senha (Opcional)</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Deixe em branco para manter a senha atual.</p>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="password" value="Nova Senha" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirmar Nova Senha" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>
    </div>
@endif
