<?php

namespace App\Http\Requests;

use App\Services\LogbookService;
use Illuminate\Foundation\Http\FormRequest;

class RunStartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_km' => ['required', 'integer', 'min:0'],
            'destinations' => ['required', 'array', 'min:1'],
            'destinations.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_km.required' => 'A quilometragem atual é obrigatória.',
            'start_km.integer' => 'A quilometragem deve ser um número inteiro.',
            'start_km.min' => 'A quilometragem não pode ser negativa.',
            'destinations.required' => 'Pelo menos um destino é obrigatório.',
            'destinations.array' => 'Os destinos devem ser em formato de lista.',
            'destinations.min' => 'Pelo menos um destino é obrigatório.',
            'destinations.*.required' => 'Cada destino é obrigatório.',
            'destinations.*.string' => 'Cada destino deve ser um texto válido.',
            'destinations.*.max' => 'Cada destino não pode ter mais de 255 caracteres.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $run = $this->route('run');

            if (!$run) {
                return;
            }

            $logbookService = app(LogbookService::class);
            $startKm = $this->input('start_km');

            // Valida o KM inicial
            $validation = $logbookService->validateStartKm($run->vehicle_id, $startKm, 100);

            if (!$validation['valid']) {
                $validator->errors()->add('start_km', $validation['message']);
            }
        });
    }
}
