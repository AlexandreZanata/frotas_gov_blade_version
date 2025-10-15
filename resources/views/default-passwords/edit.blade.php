<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Editar Senha Padrão" subtitle="Atualizar informações da senha padrão" hide-title-mobile icon="key" />
    </x-slot>
    <x-slot name="pageActions">
        <x-ui.action-icon :href="route('default-passwords.index')" icon="arrow-left" title="Voltar" variant="neutral" />
    </x-slot>

    <x-ui.card title="Informações da Senha Padrão">
        <form method="POST" action="{{ route('default-passwords.update', $defaultPassword) }}" class="space-y-6">
            @method('PUT')
            @include('default-passwords._form')

            <!-- Botões de Ação -->
            <div class="flex items-center gap-3 pt-6">
                <x-primary-button icon="save" compact>Salvar Alterações</x-primary-button>
                <a href="{{ route('default-passwords.index') }}" class="text-sm text-gray-600 dark:text-navy-200 hover:underline">Cancelar</a>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
