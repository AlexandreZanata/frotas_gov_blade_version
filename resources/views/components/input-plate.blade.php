@props([
    'id' => 'plate',
    'name' => 'plate',
    'value' => '',
    'required' => false,
    'disabled' => false,
])

<div x-data="{
    plate: '{{ $value }}'.toUpperCase(),
    error: '',
    plateType: '',

    formatPlate() {
        let value = this.plate.toUpperCase().replace(/[^A-Z0-9]/g, '');
        value = value.substring(0, 7);

        if (value.length > 3) {
            value = value.substring(0, 3) + '-' + value.substring(3);
        }

        this.plate = value;
        this.error = '';
    },

    validate() {
        const isRequired = {{ $required ? 'true' : 'false' }};

        if (!isRequired && !this.plate) {
            this.error = '';
            this.plateType = '';
            return true;
        }

        const plateClean = this.plate.replace(/[^A-Z0-9]/g, '');

        if (plateClean.length !== 7) {
            this.error = 'Placa deve conter 7 caracteres';
            this.plateType = '';
            return false;
        }

        const oldPattern = /^[A-Z]{3}[0-9]{4}$/;
        const mercosulPattern = /^[A-Z]{3}[0-9][A-Z][0-9]{2}$/;

        if (oldPattern.test(plateClean)) {
            this.error = '';
            this.plateType = 'Padrão Antigo';
            return true;
        }

        if (mercosulPattern.test(plateClean)) {
            this.error = '';
            this.plateType = 'Padrão Mercosul';
            return true;
        }

        this.error = 'Formato de placa inválido';
        this.plateType = '';
        return false;
    }
}">
    <input
        type="text"
        id="{{ $id }}"
        name="{{ $name }}"
        x-model="plate"
        @input="formatPlate()"
        @blur="validate()"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        maxlength="8"
        placeholder="ABC-1234 ou ABC1D23"
        {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm uppercase']) }}
        :class="{ 'border-red-500 dark:border-red-500': error }"
    />
    <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
    <p x-show="plateType && !error" x-text="'Formato: ' + plateType" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
</div>
