<x-guest-layout>
    <div class="flex flex-wrap min-h-screen">
        <div class="hidden md:flex w-full md:w-1/2 flex-col justify-center items-center p-12 bg-gradient-to-br from-blue-600 to-blue-800 dark:from-blue-800 dark:to-blue-900 text-white text-center min-h-[250px] md:min-h-screen">
            <div class="max-w-sm">
                <h2 class="text-3xl font-bold mb-4">Bem-vindo de volta!</h2>
                <p class="text-blue-100 opacity-90">Faça login para acessar sua conta e gerenciar o sistema Frotas Gov.</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex flex-col justify-center p-8 sm:p-12 md:py-16 bg-white dark:bg-gray-900">
            <div class="w-full max-w-md mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Frotas Gov</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">Entre com suas credenciais para continuar.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <x-input-label for="login" value="E-mail ou CPF" />
                        <x-text-input id="login" class="block mt-1 w-full dark:bg-gray-800 dark:text-white" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="seu@email.com ou 000.000.000-00" />
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <div class="mt-4 relative">
                        <x-input-label for="password" value="Senha" />
                        <x-text-input id="password" class="block mt-1 w-full pr-10 dark:bg-gray-800 dark:text-white"
                                      type="password"
                                      name="password"
                                      required autocomplete="current-password"
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
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Lembrar-me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                                {{ __('Esqueceu sua senha?') }}
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="w-full text-center justify-center">
                            <span class="w-full">{{ __('Entrar') }}</span>
                        </x-primary-button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Ainda não tem uma conta?
                        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 underline">
                            Registre-se aqui
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

        // Formatação automática de CPF
        document.getElementById('login').addEventListener('input', function (e) {
            let value = e.target.value;

            // Se parece com CPF (apenas números e pontos/traços)
            if (/^[\d.-]*$/.test(value)) {
                value = value.replace(/\D/g, '');

                if (value.length <= 11) {
                    if (value.length > 9) {
                        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                    } else if (value.length > 6) {
                        value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
                    } else if (value.length > 3) {
                        value = value.replace(/(\d{3})(\d+)/, '$1.$2');
                    }
                    e.target.value = value;
                }
            }
        });
    </script>
</x-guest-layout>
