<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Nova Senha Padrão" subtitle="Cadastrar nova senha padrão para usuários"
                          hide-title-mobile icon="key"/>
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('default-passwords.index')" icon="arrow-left" title="Voltar" variant="neutral"/>
    </x-slot>

    <x-ui.card title="Informações da Senha Padrão">
        @php($defaultPassword = new \App\Models\defect\DefaultPassword())
        <form action="{{ route('default-passwords.store') }}" method="POST" class="space-y-6">
            @include('default-passwords._form')

            <!-- Botões de Ação -->
            <div class="flex items-center gap-3 pt-6">
                <x-primary-button icon="save" compact>Salvar Senha Padrão</x-primary-button>
                <a href="{{ route('default-passwords.index') }}"
                   class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
