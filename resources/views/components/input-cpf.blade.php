@props([
    'id' => 'cpf',
    'name' => 'cpf',
    'value' => '',
    'required' => false,
    'disabled' => false,
])

<div x-data="{
    cpf: '{{ $value }}',
    error: '',
    isValid: false,

    formatCPF() {
        let value = this.cpf.replace(/\D/g, '');
        value = value.substring(0, 11);

        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }

        this.cpf = value;
        this.error = '';
    },

    validate() {
        const isRequired = {{ $required ? 'true' : 'false' }};

        if (!isRequired && !this.cpf) {
            this.error = '';
            this.isValid = false;
            return true;
        }

        const cpfClean = this.cpf.replace(/\D/g, '');

        if (cpfClean.length !== 11) {
            this.error = 'CPF deve conter 11 dígitos';
            this.isValid = false;
            return false;
        }

        if (/^(\d)\1{10}$/.test(cpfClean)) {
            this.error = 'CPF inválido';
            this.isValid = false;
            return false;
        }

        let sum = 0;
        let remainder;

        for (let i = 1; i <= 9; i++) {
            sum += parseInt(cpfClean.substring(i - 1, i)) * (11 - i);
        }
        remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpfClean.substring(9, 10))) {
            this.error = 'CPF inválido';
            this.isValid = false;
            return false;
        }

        sum = 0;
        for (let i = 1; i <= 10; i++) {
            sum += parseInt(cpfClean.substring(i - 1, i)) * (12 - i);
        }
        remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpfClean.substring(10, 11))) {
            this.error = 'CPF inválido';
            this.isValid = false;
            return false;
        }

        this.error = '';
        this.isValid = true;
        return true;
    }
}">
    <input
        type="text"
        id="{{ $id }}"
        name="{{ $name }}"
        x-model="cpf"
        @input="formatCPF()"
        @blur="validate()"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        maxlength="14"
        placeholder="000.000.000-00"
        {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm']) }}
        :class="{ 'border-red-500 dark:border-red-500': error }"
    />
    <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
    <p x-show="!error && cpf && isValid" class="mt-1 text-xs text-green-600 dark:text-green-400">✓ CPF válido</p>
</div>
