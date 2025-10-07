@props([
    'id' => 'email',
    'name' => 'email',
    'value' => '',
    'required' => false,
    'disabled' => false,
])

<div x-data="{
    email: '{{ $value }}',
    error: '',
    isValid: false,

    validate() {
        const isRequired = {{ $required ? 'true' : 'false' }};

        if (!isRequired && !this.email) {
            this.error = '';
            this.isValid = false;
            return true;
        }

        if (!this.email) {
            this.error = 'Email é obrigatório';
            this.isValid = false;
            return false;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(this.email)) {
            this.error = 'Email inválido';
            this.isValid = false;
            return false;
        }

        this.error = '';
        this.isValid = true;
        return true;
    }
}">
    <input
        type="email"
        id="{{ $id }}"
        name="{{ $name }}"
        x-model="email"
        @input="validate()"
        @blur="validate()"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        placeholder="exemplo@dominio.com"
        {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm']) }}
        :class="{ 'border-red-500 dark:border-red-500': error }"
    />
    <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
    <p x-show="!error && email && isValid" class="mt-1 text-xs text-green-600 dark:text-green-400">✓ Email válido</p>
</div>
