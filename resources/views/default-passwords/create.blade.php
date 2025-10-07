<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Senha Padrão" subtitle="Cadastrar nova senha padrão para usuários" hide-title-mobile icon="key" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('default-passwords.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações da Senha Padrão">
        @php($defaultPassword = new \App\Models\DefaultPassword())
        <form action="{{ route('default-passwords.store') }}" method="POST" class="space-y-6">
            @include('default-passwords._form')

            <!-- Botões de Ação -->
            <div class="flex items-center gap-4 pt-4">
                <x-primary-button>
                    <x-icon name="save" class="w-4 h-4 mr-2" />
                    Salvar Senha Padrão
                </x-primary-button>

                <a href="{{ route('default-passwords.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Cancelar
                </a>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>

