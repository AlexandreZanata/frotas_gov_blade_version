<x-guest-layout>
    <div class="flex flex-wrap min-h-screen">
        <div class="hidden md:flex w-full md:w-1/2 flex-col justify-center items-center p-12 bg-gradient-to-br from-blue-600 to-blue-800 dark:from-blue-800 dark:to-blue-900 text-white text-center min-h-[250px] md:min-h-screen">
            <div class="max-w-sm">
                <h2 class="text-3xl font-bold mb-4">Recupere seu Acesso</h2>
                <p class="text-blue-100 opacity-90">Insira seu e-mail para receber o link de redefinição de senha e voltar a gerenciar o sistema.</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex flex-col justify-center p-8 sm:p-12 md:py-16 bg-white dark:bg-gray-900">
            <div class="w-full max-w-md mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Frotas Gov</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                        Esqueceu sua senha? Sem problemas. Apenas nos informe seu endereço de e-mail e enviaremos um link para que você possa criar uma nova.
                    </p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('E-mail')" />
                        <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-800 dark:text-white" type="email" name="email" :value="old('email')" required autofocus placeholder="seu@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="w-full justify-center">
                            {{ __('Enviar Link de Redefinição') }}
                        </x-primary-button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Lembrou da senha?
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 underline">
                            Voltar para o login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
