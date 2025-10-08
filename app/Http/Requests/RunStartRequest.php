<?php

namespace App\Http\Requests;

use App\Services\LogbookService;
use Illuminate\Foundation\Http\FormRequest;

class RunStartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'start_km' => ['required', 'integer', 'min:0'],
            'destination' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'start_km.required' => 'A quilometragem atual é obrigatória.',
            'start_km.integer' => 'A quilometragem deve ser um número inteiro.',
            'start_km.min' => 'A quilometragem não pode ser negativa.',
            'destination.required' => 'O destino é obrigatório.',
            'destination.string' => 'O destino deve ser um texto válido.',
            'destination.max' => 'O destino não pode ter mais de 255 caracteres.',
        ];
    }

    /**
     * Configure the validator instance.
     */
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
