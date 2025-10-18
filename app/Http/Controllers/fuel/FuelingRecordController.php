<?php

namespace App\Http\Controllers\fuel;

use App\Http\Controllers\Controller;
use App\Models\fuel\Fueling;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use App\Models\fuel\FuelPrice;
use App\Models\fuel\GasStationCurrent;
use App\Models\run\Run;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FuelingRecordController extends Controller
{
    /**
     * Mostra o formulário para criar um novo abastecimento associado a uma corrida.
     *
     * @param Run $run A corrida atual.
     * @return \Illuminate\View\View
     */
    public function create(Run $run)
    {
        // Obter postos ativos (da tabela gas_stations_current)
        $currentGasStations = GasStationCurrent::where('is_active', 1)->with('gasStation')->get();

        $gasStations = $currentGasStations->map(function ($currentStation) {
            return $currentStation->gasStation;
        });

        $fuelTypes = FuelType::orderBy('name')->get();

        // Obter o tipo de combustível do veículo
        $vehicleFuelTypeId = $run->vehicle->fuel_type_id;
        $vehicleFuelType = FuelType::find($vehicleFuelTypeId);

        // Passa a corrida, postos, tipos de combustível e tipo do veículo para a view
        return view('logbook.fueling', compact('run', 'gasStations', 'fuelTypes', 'vehicleFuelTypeId', 'vehicleFuelType'));
    }

    /**
     * Armazena um novo abastecimento no banco de dados.
     *
     * @param Request $request O request HTTP com os dados do formulário.
     * @param Run $run A corrida associada.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Run $run)
    {
        // Validação dos dados de entrada
        $validatedData = $request->validate([
            'is_manual' => ['required', 'boolean'],
            'gas_station_id' => [
                Rule::requiredIf(!$request->boolean('is_manual')),
                'nullable',
                'uuid',
                'exists:gas_stations,id'
            ],
            'gas_station_name' => [ // Campo para nome manual do posto
                Rule::requiredIf($request->boolean('is_manual')),
                'nullable',
                'string',
                'max:255'
            ],
            'fuel_type_id' => ['required', 'uuid', 'exists:fuel_types,id'],
            'km' => ['required', 'integer', 'min:' . $run->start_km], // KM deve ser >= KM inicial da corrida
            'liters' => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,3})?$/'], // Maior que zero, até 3 casas decimais
            'total_value_manual' => [ // Novo campo para valor total no modo manual
                Rule::requiredIf($request->boolean('is_manual')),
                'nullable',
                'numeric',
                'gte:0',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'value_per_liter' => [ // Agora é calculado automaticamente
                'nullable',
                'numeric',
                'gte:0'
            ],
            'invoice_path' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
        ], [
            // Mensagens de erro personalizadas
            'km.min' => 'O KM do abastecimento não pode ser menor que o KM inicial da corrida.',
            'liters.regex' => 'A quantidade de litros deve ter no máximo 3 casas decimais.',
            'total_value_manual.regex' => 'O valor total deve ter no máximo 2 casas decimais.',
        ]);

        try {
            DB::beginTransaction();

            $isManual = $request->boolean('is_manual');
            $valuePerLiter = null;
            $totalValue = null;
            $gasStationId = null;
            $gasStationName = null;

            if ($isManual) {
                // Modo Manual: Usuário informa o valor total
                $totalValue = $validatedData['total_value_manual'];
                $valuePerLiter = $validatedData['value_per_liter']; // Calculado automaticamente na view
                $gasStationName = $validatedData['gas_station_name'];
            } else {
                // Modo Credenciado: Sistema calcula o valor total
                $gasStation = GasStation::find($validatedData['gas_station_id']);
                if (!$gasStation) {
                    throw new \Exception("Posto credenciado não encontrado.");
                }

                // Busca o preço por litro do posto credenciado da tabela fuel_prices
                $fuelPrice = FuelPrice::where('gas_station_id', $gasStation->id)
                    ->where('fuel_type_id', $validatedData['fuel_type_id'])
                    ->orderBy('effective_date', 'desc')
                    ->first();

                if (!$fuelPrice) {
                    Log::warning("Preço não encontrado para o posto ID: {$gasStation->id} e combustível ID: {$validatedData['fuel_type_id']}");
                    $valuePerLiter = 0;
                } else {
                    $valuePerLiter = $fuelPrice->price;
                }

                // Calcula o valor total
                $totalValue = $validatedData['liters'] * $valuePerLiter;
                $gasStationId = $gasStation->id;
            }

            // --- Tratamento da Nota Fiscal ---
            $invoicePath = null;
            if ($request->hasFile('invoice_path')) {
                // Define um nome único para o arquivo
                $fileName = 'invoice_' . time() . '_' . Str::uuid() . '.' . $request->file('invoice_path')->getClientOriginalExtension();
                // Salva no storage (ex: 'storage/app/public/invoices/fueling')
                $invoicePath = $request->file('invoice_path')->storeAs('public/invoices/fueling', $fileName);
                // Remove o 'public/' para salvar no banco o caminho acessível via URL
                $invoicePath = Str::replaceFirst('public/', '', $invoicePath);
            }

            // Cria o registro de abastecimento
            $fueling = Fueling::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $run->vehicle_id,
                'run_id' => $run->id,
                'fuel_type_id' => $validatedData['fuel_type_id'],
                'gas_station_id' => $gasStationId, // Será null se for manual
                'fueled_at' => now(),
                'km' => $validatedData['km'],
                'liters' => $validatedData['liters'],
                'value_per_liter' => $valuePerLiter,
                'value' => $totalValue,
                'invoice_path' => $invoicePath,
                'public_code' => $this->generatePublicCode(),
                'signature_id' => null,
                'viewed_by' => null,
            ]);

            DB::commit();

            // Redireciona de volta para a tela de finalizar corrida com mensagem de sucesso
            return redirect()->route('logbook.finish', $run)->with('success', 'Abastecimento registrado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao registrar abastecimento: " . $e->getMessage(), [
                'user_id' => Auth::id(),
                'run_id' => $run->id,
                'exception' => $e
            ]);

            // Tenta limpar arquivos salvos se a transação falhou
            if (isset($invoicePath) && Storage::exists('public/' . $invoicePath)) {
                Storage::delete('public/' . $invoicePath);
            }

            // Redireciona de volta com erro
            return back()->withInput()->with('error', 'Erro ao registrar abastecimento: ' . $e->getMessage());
        }
    }

    /**
     * Gera um código público único para o abastecimento.
     *
     * @return string
     */
    private function generatePublicCode(): string
    {
        do {
            // Exemplo: ABS-YYYYMMDD-XXXXXX (6 caracteres aleatórios)
            $code = 'ABS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Fueling::where('public_code', $code)->exists());

        return $code;
    }
}
