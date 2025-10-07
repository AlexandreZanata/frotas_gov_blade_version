<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Novo Usuário" subtitle="Cadastrar novo usuário no sistema" hide-title-mobile icon="user-plus" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('users.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações do Usuário">
        @php($user = new \App\Models\User())
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @include('users._form')

            <!-- Botões de Ação -->
            <div class="flex items-center gap-4 pt-4">
                <x-primary-button>
                    <x-icon name="save" class="w-4 h-4 mr-2" />
                    Salvar Usuário
                </x-primary-button>

                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Cancelar
                </a>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>

