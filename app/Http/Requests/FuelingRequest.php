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
        $isManual = $this->input('is_manual', false);

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
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Run;

class RunStartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_km' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    // Validação: KM inicial deve ser >= ao KM final da última corrida
                    $lastRun = Run::where('vehicle_id', $this->route('run')->vehicle_id)
                        ->where('status', 'completed')
                        ->latest('finished_at')
                        ->first();

                    if ($lastRun && $value < $lastRun->end_km) {
                        $fail("O KM inicial ({$value}) não pode ser menor que o KM final da última corrida ({$lastRun->end_km}).");
                    }
                },
            ],
            'destination' => 'required|string|max:255',
            'origin' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'start_km.required' => 'O KM inicial é obrigatório.',
            'start_km.numeric' => 'O KM inicial deve ser um número.',
            'start_km.min' => 'O KM inicial não pode ser negativo.',
            'destination.required' => 'O destino é obrigatório.',
            'destination.max' => 'O destino não pode ter mais de 255 caracteres.',
        ];
    }
}

