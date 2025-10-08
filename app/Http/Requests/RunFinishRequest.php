<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunFinishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'end_km' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $run = $this->route('run');
                    if ($value < $run->start_km) {
                        $fail("O KM final ({$value}) não pode ser menor que o KM inicial ({$run->start_km}).");
                    }

                    $distance = $value - $run->start_km;
                    $maxDistance = 500;

                    if ($distance > $maxDistance) {
                        $fail("A distância percorrida ({$distance}km) excede o limite máximo de {$maxDistance}km por corrida.");
                    }
                },
            ],
            'stop_point' => 'nullable|string|max:255',
        ];

        // Validações de abastecimento (se o checkbox estiver marcado)
        if ($this->has('add_fueling')) {
            $rules['fueling_km'] = 'required|numeric|min:' . $this->route('run')->start_km;
            $rules['liters'] = 'required|numeric|min:0.01';
            $rules['fuel_type_id'] = 'required|exists:fuel_types,id';
            $rules['fueling_type'] = 'required|in:credenciado,manual';

            if ($this->input('fueling_type') === 'credenciado') {
                $rules['gas_station_id'] = 'required|exists:gas_stations,id';
            } else {
                $rules['gas_station_name'] = 'required|string|max:255';
                $rules['total_value'] = 'required|numeric|min:0';
            }

            $rules['invoice'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'end_km.required' => 'O KM final é obrigatório.',
            'end_km.numeric' => 'O KM final deve ser um número.',
            'stop_point.required' => 'O ponto de parada é obrigatório.',
            'fueling_km.required' => 'O KM de abastecimento é obrigatório.',
            'liters.required' => 'A quantidade de litros é obrigatória.',
            'fuel_type_id.required' => 'O tipo de combustível é obrigatório.',
            'gas_station_id.required' => 'Selecione um posto credenciado.',
            'gas_station_name.required' => 'O nome do posto é obrigatório.',
            'total_value.required' => 'O valor do abastecimento é obrigatório.',
        ];
    }
}
