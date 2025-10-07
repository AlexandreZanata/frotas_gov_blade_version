@props([
    'id' => 'cnh_expiration_date',
    'name' => 'cnh_expiration_date',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'minDate' => 'today',
])

<div x-data="{
    date: '{{ $value }}',
    error: '',
    warning: '',
    minDateValue: '',

    init() {
        const minDateProp = '{{ $minDate }}';
        if (minDateProp === 'today') {
            this.minDateValue = new Date().toISOString().split('T')[0];
        } else if (minDateProp !== 'none') {
            this.minDateValue = minDateProp;
        }

        if (this.date) {
            this.validate();
        }
    },

    validate() {
        const isRequired = {{ $required ? 'true' : 'false' }};
        const fieldId = '{{ $id }}';
        const minDateProp = '{{ $minDate }}';

        if (!isRequired && !this.date) {
            this.error = '';
            this.warning = '';
            return true;
        }

        if (!this.date) {
            this.error = 'Data é obrigatória';
            this.warning = '';
            return false;
        }

        const selectedDate = new Date(this.date + 'T00:00:00');
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (isNaN(selectedDate.getTime())) {
            this.error = 'Data inválida';
            this.warning = '';
            return false;
        }

        if (fieldId.includes('cnh') && fieldId.includes('expiration')) {
            const diffDays = Math.ceil((selectedDate - today) / (1000 * 60 * 60 * 24));

            if (diffDays < 0) {
                this.error = 'CNH vencida';
                this.warning = '';
                return false;
            }

            if (diffDays <= 30) {
                this.error = '';
                this.warning = 'CNH vence em ' + diffDays + ' dia(s)';
            } else {
                this.error = '';
                this.warning = '';
            }
        } else if (minDateProp === 'today' && selectedDate < today) {
            this.error = 'Data não pode ser anterior a hoje';
            this.warning = '';
            return false;
        } else {
            this.error = '';
            this.warning = '';
        }

        return true;
    }
}">
    <input
        type="date"
        id="{{ $id }}"
        name="{{ $name }}"
        x-model="date"
        @blur="validate()"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        :min="minDateValue"
        {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-navy-800 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm']) }}
        :class="{ 'border-red-500 dark:border-red-500': error }"
    />
    <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
    <p x-show="warning" x-text="warning" class="mt-1 text-sm text-yellow-600 dark:text-yellow-400"></p>
</div>
