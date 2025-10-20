<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Adicionar Usuário de Lixo"
            subtitle="Vincule um usuário ao sistema de coleta"
            hide-title-mobile
            icon="user-plus"
        />
    </x-slot>

    <x-ui.card>
        <form action="{{ route('admin.garbage-users.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <x-input-label for="user_id" value="Selecionar Usuário *" />
                    <x-ui.select name="user_id" id="user_id" class="mt-2" required>
                        <option value="">Selecione um usuário...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                {{ $user->name }} - {{ $user->email }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                    @if($users->isEmpty())
                        <p class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">
                            Todos os usuários já estão vinculados ao sistema de lixo.
                        </p>
                    @endif
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-navy-700">
                    <a href="{{ route('admin.garbage-users.index') }}">
                        <x-secondary-button type="button">
                            <x-icon name="arrow-left" class="w-4 h-4 mr-2" />
                            Cancelar
                        </x-secondary-button>
                    </a>

                    <x-primary-button type="submit" :disabled="$users->isEmpty()">
                        <x-icon name="save" class="w-4 h-4 mr-2" />
                        Criar Usuário
                    </x-primary-button>
                </div>
            </div>
        </form>
    </x-ui.card>
</x-app-layout>
