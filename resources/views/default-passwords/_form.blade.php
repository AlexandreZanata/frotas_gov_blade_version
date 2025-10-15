@csrf
<div class="grid gap-4 md:grid-cols-1">
    <div>
        <x-input-label for="name" value="Nome Identificador *" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $defaultPassword->name ?? '')" required placeholder="ex: reset_password, driver_default" />
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nome único para identificar esta senha (sem espaços)</p>
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="description" value="Descrição" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">{{ old('description', $defaultPassword->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="password" value="Senha {{ $defaultPassword->exists ?? false ? '' : '*' }}" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="!($defaultPassword->exists ?? false)" />
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            @if($defaultPassword->exists ?? false)
                Deixe em branco para manter a senha atual
            @else
                Senha que será atribuída aos usuários (mínimo 8 caracteres)
            @endif
        </p>
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="password_confirmation" value="Confirmar Senha {{ $defaultPassword->exists ?? false ? '' : '*' }}" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :required="!($defaultPassword->exists ?? false)" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $defaultPassword->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-navy-800">
        <x-input-label for="is_active" value="Senha ativa (disponível para uso)" class="!mb-0" />
    </div>
</div>
