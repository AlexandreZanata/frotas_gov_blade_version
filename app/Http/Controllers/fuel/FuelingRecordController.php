<?php

namespace App\Http\Controllers\fuel;

use App\Http\Controllers\Controller;
use App\Models\fuel\Fueling;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
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
        // Verifica se o veículo pertence à corrida correta (segurança)
        // (Adicionar lógica de autorização/política se necessário)

        $gasStations = GasStation::where('status', 'active')->orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();

        // Passa a corrida, postos e tipos de combustível para a view
        return view('logbook.fueling', compact('run', 'gasStations', 'fuelTypes'));
        // Nota: A view 'fueling.blade.php' precisa existir em resources/views/
        // O nome do arquivo que você passou foi fueling.blade.php, então o nome da view é 'fueling'.
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
            'value_per_liter' => [
                Rule::requiredIf($request->boolean('is_manual')),
                'nullable',
                'numeric',
                'gte:0', // Maior ou igual a zero
                'regex:/^\d+(\.\d{1,2})?$/' // Até 2 casas decimais
            ],
            'invoice_path' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
            'signature_data' => ['required', 'string'], // Espera a assinatura como Data URL (string base64)
        ], [
            // Mensagens de erro personalizadas (opcional)
            'km.min' => 'O KM do abastecimento não pode ser menor que o KM inicial da corrida.',
            'liters.regex' => 'A quantidade de litros deve ter no máximo 3 casas decimais.',
            'value_per_liter.regex' => 'O valor por litro deve ter no máximo 2 casas decimais.',
            'signature_data.required' => 'A assinatura é obrigatória.',
        ]);

        try {
            DB::beginTransaction();

            $isManual = $request->boolean('is_manual');
            $valuePerLiter = null;
            $gasStationId = null;
            $gasStationName = null; // Para guardar o nome manual

            if ($isManual) {
                $valuePerLiter = $validatedData['value_per_liter'];
                $gasStationName = $validatedData['gas_station_name'];
                // Em modo manual, não temos um gas_station_id, mas podemos querer
                // criar um registro genérico ou associar a um "posto manual" se existir.
                // Por agora, deixaremos gas_station_id como null.
                // Poderíamos também salvar o nome manual em um campo diferente na tabela fuelings,
                // mas a estrutura atual não tem isso. Usaremos gas_station_id = null.
            } else {
                $gasStation = GasStation::find($validatedData['gas_station_id']);
                if (!$gasStation) {
                    throw new \Exception("Posto credenciado não encontrado.");
                }
                // Busca o preço por litro do posto credenciado (PRECISA ADICIONAR ESSA LÓGICA NO MODEL/BD)
                // $valuePerLiter = $gasStation->price_per_liter; // Supondo que existe essa propriedade/relação
                // Temporário - Se não tiver o preço no cadastro do posto, lança erro ou define um padrão
                if (!isset($gasStation->price_per_liter)) {
                    Log::warning("Preço por litro não definido para o posto credenciado ID: {$gasStation->id}");
                    // Poderia lançar uma exceção ou usar um valor padrão/null se a regra permitir
                    // throw new \Exception("Preço por litro não configurado para o posto selecionado.");
                    $valuePerLiter = 0; // Ou null, dependendo da regra de negócio e BD
                } else {
                    $valuePerLiter = $gasStation->price_per_liter;
                }
                $gasStationId = $gasStation->id;
            }

            // Calcula o valor total
            $totalValue = $validatedData['liters'] * $valuePerLiter;

            // --- Tratamento da Assinatura ---
            // 1. Decodificar Base64 e Salvar Imagem
            $signatureImage = $this->saveSignature($validatedData['signature_data']);
            if (!$signatureImage) {
                throw new \Exception("Falha ao salvar a imagem da assinatura.");
            }
            $signaturePath = $signatureImage['path']; // Caminho relativo no storage

            // 2. Salvar na tabela fuelings_signatures (NECESSITA MODEL e MIGRATION)
            // Assumindo que a tabela fuelings_signatures tem 'id', 'path', 'user_id', 'created_at', etc.
            /*
            $signatureRecord = FuelingSignature::create([
                'user_id' => Auth::id(),
                'path' => $signaturePath,
                // outros campos se houver...
            ]);
            $signatureId = $signatureRecord->id;
            */
            // **Placeholder:** Como não temos a tabela/model `FuelingSignature`, vamos simular o ID ou lançar erro.
            // Para continuar, usaremos NULL, mas isso PRECISA SER IMPLEMENTADO CORRETAMENTE.
            $signatureId = null;
            Log::warning("Tabela/Model FuelingSignature não implementado. signature_id será null.");
            // throw new \Exception("Lógica de salvamento da assinatura (tabela fuelings_signatures) não implementada.");


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
                // Se precisar salvar o nome manual, adicione um campo na tabela ou use gas_station_id de forma especial
                'fueled_at' => now(), // Ou pegar de um campo de data/hora do formulário se existir
                'km' => $validatedData['km'],
                'liters' => $validatedData['liters'],
                'value_per_liter' => $valuePerLiter, // Pode ser null se for credenciado e o BD permitir
                'value' => $totalValue, // Valor total calculado
                'invoice_path' => $invoicePath,
                'public_code' => $this->generatePublicCode(), // Gerar código único
                'signature_id' => $signatureId, // ID da assinatura salva na tabela fuelings_signatures
                'viewed_by' => null, // Inicialmente null
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
            if (isset($signaturePath) && Storage::exists('public/' . $signaturePath)) {
                Storage::delete('public/' . $signaturePath);
            }
            if (isset($invoicePath) && Storage::exists('public/' . $invoicePath)) {
                Storage::delete('public/' . $invoicePath);
            }


            // Redireciona de volta com erro
            return back()->withInput()->with('error', 'Erro ao registrar abastecimento: ' . $e->getMessage());
        }
    }

    /**
     * Salva a imagem da assinatura (decodifica base64 e armazena).
     *
     * @param string $base64Data Data URL da assinatura (e.g., "data:image/png;base64,...").
     * @return array|false Retorna ['path' => caminho_relativo] ou false em caso de erro.
     */
    private function saveSignature(string $base64Data)
    {
        // Verifica se é um Data URL válido
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            Log::error("Formato inválido para Data URL da assinatura.");
            return false;
        }

        // Extrai os dados da imagem (remove o prefixo 'data:image/png;base64,')
        $imageData = base64_decode(substr($base64Data, strpos($base64Data, ',') + 1));
        $type = strtolower($type[1]); // png, jpg, etc.

        if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
            Log::error("Tipo de imagem inválido para assinatura: " . $type);
            return false;
        }

        if ($imageData === false) {
            Log::error("Falha ao decodificar base64 da assinatura.");
            return false;
        }

        // Gera um nome de arquivo único
        $filename = 'signature_' . time() . '_' . Str::uuid() . '.' . $type;
        $relativePath = 'signatures/fueling/' . $filename; // Caminho dentro de storage/app/public/

        try {
            // Salva o arquivo
            Storage::disk('public')->put($relativePath, $imageData);
            return ['path' => $relativePath]; // Retorna o caminho relativo para salvar no BD
        } catch (\Exception $e) {
            Log::error("Erro ao salvar arquivo de assinatura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gera um código público único para o abastecimento.
     * (Esta é uma implementação simples, pode precisar de ajustes
     * para garantir unicidade em alta concorrência).
     *
     * @return string
     */
    private function generatePublicCode(): string
    {
        do {
            // Exemplo: ABS-YYYYMMDD-XXXXXX (6 caracteres aleatórios)
            $code = 'ABS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Fueling::where('public_code', $code)->exists()); // Verifica se já existe

        return $code;
    }

}

