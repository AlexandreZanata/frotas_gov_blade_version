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
        return [
            'end_km' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $run = $this->route('run');
                    if ($value < $run->start_km) {
                        $fail("O KM final ({$value}) não pode ser menor que o KM inicial ({$run->start_km}).");
                    }

                    // Validação de autonomia máxima (exemplo: 500km por corrida)
                    $distance = $value - $run->start_km;
                    $maxDistance = 500; // Pode ser configurável por categoria de veículo

                    if ($distance > $maxDistance) {
                        $fail("A distância percorrida ({$distance}km) excede o limite máximo de {$maxDistance}km por corrida.");
                    }
                },
            ],
            'stop_point' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'end_km.required' => 'O KM final é obrigatório.',
            'end_km.numeric' => 'O KM final deve ser um número.',
        ];
    }
}

