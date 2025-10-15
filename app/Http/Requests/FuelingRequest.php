<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FuelingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isManual = $this->boolean('is_manual');

        return [
            'gas_station_id' => $isManual ? 'nullable' : 'required|exists:gas_stations,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'km' => 'required|numeric|min:0',
            'liters' => 'required|numeric|min:0.1|max:1000',
            'value_per_liter' => $isManual ? 'required|numeric|min:0' : 'nullable',
            'invoice_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'signature_path' => 'required|string',
            'is_manual' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'gas_station_id.required' => 'Selecione um posto de gasolina.',
            'fuel_type_id.required' => 'Selecione o tipo de combustível.',
            'km.required' => 'O KM atual é obrigatório.',
            'liters.required' => 'A quantidade de litros é obrigatória.',
            'liters.min' => 'A quantidade mínima é 0.1 litro.',
            'value_per_liter.required' => 'O valor por litro é obrigatório para abastecimento manual.',
            'signature_path.required' => 'A assinatura é obrigatória.',
        ];
    }
}
