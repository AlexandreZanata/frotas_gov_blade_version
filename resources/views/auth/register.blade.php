{{-- resources/views/auth/register.blade.php --}}
<x-guest-layout>
    <div class="flex flex-wrap min-h-screen">
        <div class="hidden md:flex w-full md:w-1/2 flex-col justify-center items-center p-12 bg-gradient-to-br from-blue-600 to-blue-800 dark:from-blue-800 dark:to-blue-900 text-white text-center min-h-[250px] md:min-h-screen">
            <div class="max-w-sm">
                <h2 class="text-3xl font-bold mb-4">Junte-se a nós!</h2>
                <p class="text-blue-100 opacity-90">Crie sua conta para acessar o sistema Frotas Gov. Seu acesso será liberado após aprovação.</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex flex-col justify-center p-8 sm:p-12 md:py-16 bg-white dark:bg-gray-900">
            <div class="w-full max-w-md mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Frotas Gov</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">Seu acesso será liberado após aprovação.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nome Completo" />
                        <x-text-input id="name" class="block mt-1 w-full dark:bg-gray-800 dark:text-white" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Seu nome completo" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="cpf" value="CPF" />
                        <x-text-input id="cpf" class="block mt-1 w-full dark:bg-gray-800 dark:text-white" type="text" name="cpf" :value="old('cpf')" required autocomplete="cpf" placeholder="000.000.000-00" maxlength="14" />
                        <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                        <p id="cpf-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-800 dark:text-white" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="seu@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        <p id="email-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="secretariat_id" value="Secretaria" />
                        <select id="secretariat_id" name="secretariat_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                            <option value="">Selecione sua secretaria...</option>
                            @foreach($secretariats as $secretariat)
                                <option value="{{ $secretariat->id }}" {{ old('secretariat_id') == $secretariat->id ? 'selected' : '' }}>
                                    {{ $secretariat->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('secretariat_id')" class="mt-2" />
                    </div>

                    <div class="mt-4 relative">
                        <x-input-label for="password" value="Senha" />
                        <x-text-input id="password" class="block mt-1 w-full pr-10 dark:bg-gray-800 dark:text-white"
                                      type="password"
                                      name="password"
                                      required autocomplete="new-password"
                                      placeholder="Sua senha" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer pt-6" onclick="togglePassword('password')">
                            <svg id="eye-icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-off-icon-password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />

                        <div class="mt-2">
                            <div class="flex items-center mb-1">
                                <div class="h-2 flex-1 rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div id="password-strength-bar" class="h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span id="password-strength-text" class="text-gray-500 dark:text-gray-400">Força da senha</span>
                                <span id="password-requirements" class="text-red-500 text-xs"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 relative">
                        <x-input-label for="password_confirmation" value="Confirmar Senha" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10 dark:bg-gray-800 dark:text-white"
                                      type="password"
                                      name="password_confirmation"
                                      required autocomplete="new-password"
                                      placeholder="Confirme sua senha" />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer pt-6" onclick="togglePassword('password_confirmation')">
                            <svg id="eye-icon-password_confirmation" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-off-icon-password_confirmation" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </div>
                        <p id="password-confirm-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="w-full text-center justify-center" id="submit-button">
                            <span class="w-full">{{ __('Registrar') }}</span>
                        </x-primary-button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Já tem uma conta?
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 underline">
                            Faça login aqui
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(`eye-icon-${fieldId}`);
            const eyeOffIcon = document.getElementById(`eye-off-icon-${fieldId}`);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        // Formatação do CPF
        document.getElementById('cpf').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');

            if (value.length > 11) {
                value = value.slice(0, 11);
            }

            if (value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/(\d{3})(\d+)/, '$1.$2');
            }

            e.target.value = value;

            // Validação do CPF
            validateCPF(value);
        });

        // Validação de CPF
        function validateCPF(cpf) {
            const cpfError = document.getElementById('cpf-error');
            cpf = cpf.replace(/\D/g, '');

            if (cpf.length === 0) {
                cpfError.classList.add('hidden');
                return true;
            }

            if (cpf.length !== 11) {
                cpfError.textContent = 'CPF deve ter 11 dígitos';
                cpfError.classList.remove('hidden');
                return false;
            }

            // Validação dos dígitos verificadores
            if (/^(\d)\1{10}$/.test(cpf)) {
                cpfError.textContent = 'CPF inválido';
                cpfError.classList.remove('hidden');
                return false;
            }

            let sum = 0;
            for (let i = 0; i < 9; i++) {
                sum += parseInt(cpf.charAt(i)) * (10 - i);
            }

            let remainder = 11 - (sum % 11);
            let digit1 = remainder >= 10 ? 0 : remainder;

            sum = 0;
            for (let i = 0; i < 10; i++) {
                sum += parseInt(cpf.charAt(i)) * (11 - i);
            }

            remainder = 11 - (sum % 11);
            let digit2 = remainder >= 10 ? 0 : remainder;

            if (parseInt(cpf.charAt(9)) !== digit1 || parseInt(cpf.charAt(10)) !== digit2) {
                cpfError.textContent = 'CPF inválido';
                cpfError.classList.remove('hidden');
                return false;
            }

            cpfError.classList.add('hidden');
            return true;
        }

        // Validação de senha em tempo real
        document.getElementById('password').addEventListener('input', function (e) {
            const password = e.target.value;
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');
            const requirements = document.getElementById('password-requirements');

            let strength = 0;
            let missing = [];

            if (password.length >= 8) {
                strength += 25;
            } else {
                missing.push('8 caracteres');
            }

            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                strength += 25;
            } else {
                missing.push('maiúsculas e minúsculas');
            }

            if (/\d/.test(password)) {
                strength += 25;
            } else {
                missing.push('números');
            }

            if (/[^a-zA-Z0-9]/.test(password)) {
                strength += 25;
            } else {
                missing.push('caracteres especiais');
            }

            strengthBar.style.width = strength + '%';

            if (strength < 50) {
                strengthBar.style.backgroundColor = '#ef4444';
                strengthText.textContent = 'Fraca';
                strengthText.className = 'text-red-500 text-xs';
            } else if (strength < 75) {
                strengthBar.style.backgroundColor = '#f59e0b';
                strengthText.textContent = 'Média';
                strengthText.className = 'text-orange-500 text-xs';
            } else if (strength < 100) {
                strengthBar.style.backgroundColor = '#3b82f6';
                strengthText.textContent = 'Boa';
                strengthText.className = 'text-blue-500 text-xs';
            } else {
                strengthBar.style.backgroundColor = '#10b981';
                strengthText.textContent = 'Excelente';
                strengthText.className = 'text-green-500 text-xs';
            }

            if (missing.length > 0) {
                requirements.textContent = 'Falta: ' + missing.join(', ');
            } else {
                requirements.textContent = '';
            }
        });

        // Validação de confirmação de senha
        document.getElementById('password_confirmation').addEventListener('input', function (e) {
            const password = document.getElementById('password').value;
            const confirmation = e.target.value;
            const error = document.getElementById('password-confirm-error');

            if (confirmation.length > 0 && password !== confirmation) {
                error.textContent = 'As senhas não conferem';
                error.classList.remove('hidden');
            } else {
                error.classList.add('hidden');
            }
        });
    </script>
</x-guest-layout>
