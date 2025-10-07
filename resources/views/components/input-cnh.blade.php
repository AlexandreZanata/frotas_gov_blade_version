@props([
    'id' => 'cnh',
    'name' => 'cnh',
    'value' => '',
    'required' => false,
    'disabled' => false,
])

<div x-data="{
    cnh: '{{ $value }}',
    error: '',

    formatCNH() {
        let value = this.cnh.replace(/\D/g, '');
        value = value.substring(0, 11);
        this.cnh = value;
        this.error = '';
    },

    validate() {
        const isRequired = {{ $required ? 'true' : 'false' }};

        if (!isRequired && !this.cnh) {
            this.error = '';
            return true;
        }

        const cnhClean = this.cnh.replace(/\D/g, '');

        if (cnhClean.length !== 11) {
            this.error = 'CNH deve conter 11 dígitos';
            return false;
        }

        if (/^(\d)\1{10}$/.test(cnhClean)) {
            this.error = 'CNH inválida';
            return false;
        }

        let v = 0;
        let j = 9;

        for (let i = 0; i < 9; i++, j--) {
            v += parseInt(cnhClean.charAt(i)) * j;
        }

        let dsc = 0;
        let vl1 = v % 11;

        if (vl1 >= 10) {
            vl1 = 0;
            dsc = 2;
        }

        v = 0;
        j = 1;

        for (let i = 0; i < 9; i++, j++) {
            v += parseInt(cnhClean.charAt(i)) * j;
        }

        let x = v % 11;
        let vl2 = x >= 10 ? 0 : x - dsc;

        const dv = vl1.toString() + vl2.toString();

        if (dv !== cnhClean.substring(9, 11)) {
            this.error = 'CNH inválida';
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
        x-model="cnh"
        @input="formatCNH()"
        @blur="validate()"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        maxlength="11"
        placeholder="00000000000"
        {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm']) }}
        :class="{ 'border-red-500 dark:border-red-500': error }"
    />
    <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
</div>
