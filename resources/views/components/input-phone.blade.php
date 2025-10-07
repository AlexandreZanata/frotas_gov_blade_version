@props([
    'id' => 'phone',
    'name' => 'phone',
    'value' => '',
    'required' => false,
    'disabled' => false,
])

<div x-data="{
    phone: '{{ $value }}',
    error: '',

    formatPhone() {
        let value = this.phone.replace(/\D/g, '');
        value = value.substring(0, 11);

        if (value.length <= 11) {
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
        }

        this.phone = value;
        this.error = '';
    },

    validate() {
        const isRequired = {{ $required ? 'true' : 'false' }};

        if (!isRequired && !this.phone) {
            this.error = '';
            return true;
        }

        const phoneClean = this.phone.replace(/\D/g, '');

        if (phoneClean.length < 10 || phoneClean.length > 11) {
            this.error = 'Telefone deve conter 10 ou 11 dígitos';
            return false;
        }

        const ddd = parseInt(phoneClean.substring(0, 2));
        if (ddd < 11 || ddd > 99) {
            this.error = 'DDD inválido';
            return false;
        }

        this.error = '';
        return true;
    }
}">
    <input
        type="text"
        id="{{ $id }}"
        name="{{ $name }}"
        x-model="phone"
        @input="formatPhone()"
        @blur="validate()"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        maxlength="15"
        placeholder="(00) 00000-0000"
        {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm']) }}
        :class="{ 'border-red-500 dark:border-red-500': error }"
    />
    <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
</div>
