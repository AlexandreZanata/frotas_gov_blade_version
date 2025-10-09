<?php

namespace App\Http\Controllers;

use App\Models\FuelQuotation;
use App\Models\FuelQuotationPrice;
use App\Models\FuelQuotationDiscount;
use App\Models\FuelPumpPrice;
use App\Models\GasStation;
use App\Models\FuelType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FuelQuotationController extends Controller
{
    /**
     * Listar todas as cotações
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $quotations = FuelQuotation::with(['user'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('quotation_date', 'like', "%{$search}%");
            })
            ->orderBy('quotation_date', 'desc')
            ->paginate(15);

        return view('fuel-quotations.index', compact('quotations', 'search'));
    }

    /**
     * Mostrar formulário de nova cotação
     */
    public function create()
    {
        $gasStations = GasStation::where('status', 'active')->orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();

        return view('fuel-quotations.create', compact('gasStations', 'fuelTypes'));
    }

    /**
     * Salvar nova cotação
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quotation_date' => 'required|date',
            'calculation_method' => 'required|in:simple_average,custom',
            'notes' => 'nullable|string',

            // Preços coletados
            'prices' => 'required|array|min:1',
            'prices.*.gas_station_id' => 'required|exists:gas_stations,id',
            'prices.*.fuel_type_id' => 'required|exists:fuel_types,id',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.evidence' => 'nullable|image|max:5120',

            // Descontos
            'discounts' => 'required|array|min:1',
            'discounts.*.fuel_type_id' => 'required|exists:fuel_types,id',
            'discounts.*.discount_percentage' => 'required|numeric|min:0|max:100',

            // Preços de bomba (opcionais)
            'pump_prices' => 'nullable|array',
            'pump_prices.*.gas_station_id' => 'required|exists:gas_stations,id',
            'pump_prices.*.fuel_type_id' => 'required|exists:fuel_types,id',
            'pump_prices.*.pump_price' => 'required|numeric|min:0',
            'pump_prices.*.evidence' => 'nullable|image|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // Criar cotação
            $quotation = FuelQuotation::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'quotation_date' => $request->quotation_date,
                'calculation_method' => $request->calculation_method,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // Salvar preços coletados
            foreach ($request->prices as $index => $priceData) {
                $evidencePath = null;
                if (isset($priceData['evidence'])) {
                    $evidencePath = $priceData['evidence']->store('fuel-quotations/evidence', 'public');
                }

                FuelQuotationPrice::create([
                    'fuel_quotation_id' => $quotation->id,
                    'gas_station_id' => $priceData['gas_station_id'],
                    'fuel_type_id' => $priceData['fuel_type_id'],
                    'price' => $priceData['price'],
                    'evidence_path' => $evidencePath,
                ]);
            }

            // Calcular médias e salvar descontos
            $averages = $quotation->calculateAverages();

            foreach ($request->discounts as $discountData) {
                $fuelTypeId = $discountData['fuel_type_id'];
                $averagePrice = $averages[$fuelTypeId] ?? 0;
                $discountPercentage = $discountData['discount_percentage'];
                $finalPrice = $averagePrice - ($averagePrice * ($discountPercentage / 100));

                FuelQuotationDiscount::create([
                    'fuel_quotation_id' => $quotation->id,
                    'fuel_type_id' => $fuelTypeId,
                    'average_price' => $averagePrice,
                    'discount_percentage' => $discountPercentage,
                    'final_price' => round($finalPrice, 3),
                ]);
            }

            // Salvar preços de bomba se fornecidos
            if ($request->has('pump_prices')) {
                foreach ($request->pump_prices as $pumpData) {
                    $evidencePath = null;
                    if (isset($pumpData['evidence'])) {
                        $evidencePath = $pumpData['evidence']->store('fuel-quotations/pump-evidence', 'public');
                    }

                    FuelPumpPrice::create([
                        'fuel_quotation_id' => $quotation->id,
                        'gas_station_id' => $pumpData['gas_station_id'],
                        'fuel_type_id' => $pumpData['fuel_type_id'],
                        'pump_price' => $pumpData['pump_price'],
                        'evidence_path' => $evidencePath,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('fuel-quotations.show', $quotation)
                ->with('success', 'Cotação criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao criar cotação: ' . $e->getMessage());
        }
    }

    /**
     * Exibir cotação específica
     */
    public function show(FuelQuotation $fuelQuotation)
    {
        $fuelQuotation->load([
            'user',
            'prices.gasStation',
            'prices.fuelType',
            'discounts.fuelType',
            'pumpPrices.gasStation',
            'pumpPrices.fuelType',
        ]);

        // Preparar dados para tabela comparativa
        $comparison = $this->buildComparisonTable($fuelQuotation);

        return view('fuel-quotations.show', compact('fuelQuotation', 'comparison'));
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit(FuelQuotation $fuelQuotation)
    {
        // Não permitir editar cotações já completadas
        if ($fuelQuotation->status === 'completed') {
            return redirect()->route('fuel-quotations.show', $fuelQuotation)
                ->with('error', 'Não é possível editar cotações finalizadas.');
        }

        $gasStations = GasStation::where('status', 'active')->orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();

        $fuelQuotation->load(['prices', 'discounts', 'pumpPrices']);

        return view('fuel-quotations.edit', compact('fuelQuotation', 'gasStations', 'fuelTypes'));
    }

    /**
     * Atualizar cotação
     */
    public function update(Request $request, FuelQuotation $fuelQuotation)
    {
        // Implementar lógica similar ao store
        // Por segurança, não permitir editar cotações finalizadas
        if ($fuelQuotation->status === 'completed') {
            return back()->with('error', 'Não é possível editar cotações finalizadas.');
        }

        // Lógica de atualização similar ao store
        return back()->with('info', 'Funcionalidade de edição em desenvolvimento.');
    }

    /**
     * Excluir cotação
     */
    public function destroy(FuelQuotation $fuelQuotation)
    {
        try {
            // Deletar arquivos de evidências
            foreach ($fuelQuotation->prices as $price) {
                if ($price->evidence_path) {
                    Storage::disk('public')->delete($price->evidence_path);
                }
            }

            foreach ($fuelQuotation->pumpPrices as $pumpPrice) {
                if ($pumpPrice->evidence_path) {
                    Storage::disk('public')->delete($pumpPrice->evidence_path);
                }
            }

            $fuelQuotation->delete();

            return redirect()->route('fuel-quotations.index')
                ->with('success', 'Cotação excluída com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir cotação: ' . $e->getMessage());
        }
    }

    /**
     * Construir tabela comparativa
     */
    private function buildComparisonTable(FuelQuotation $quotation): array
    {
        $comparison = [];

        foreach ($quotation->discounts as $discount) {
            $fuelType = $discount->fuelType;

            // Buscar preços de bomba para este combustível
            $pumpPrices = $quotation->pumpPrices()
                ->where('fuel_type_id', $fuelType->id)
                ->with('gasStation')
                ->get();

            $comparison[] = [
                'fuel_type' => $fuelType->name,
                'average_price' => $discount->average_price,
                'discount_percentage' => $discount->discount_percentage,
                'final_price' => $discount->final_price,
                'pump_prices' => $pumpPrices,
            ];
        }

        return $comparison;
    }

    /**
     * API: Calcular médias em tempo real
     */
    public function calculateAverages(Request $request)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.fuel_type_id' => 'required|exists:fuel_types,id',
            'prices.*.price' => 'required|numeric|min:0',
            'method' => 'required|in:simple_average,custom',
        ]);

        $averages = [];
        $pricesByFuelType = [];

        // Agrupar preços por tipo de combustível
        foreach ($request->prices as $price) {
            $fuelTypeId = $price['fuel_type_id'];
            if (!isset($pricesByFuelType[$fuelTypeId])) {
                $pricesByFuelType[$fuelTypeId] = [];
            }
            $pricesByFuelType[$fuelTypeId][] = $price['price'];
        }

        // Calcular média
        foreach ($pricesByFuelType as $fuelTypeId => $prices) {
            if ($request->method === 'simple_average') {
                $average = array_sum($prices) / count($prices);
            } else {
                // Método personalizado
                $average = array_sum($prices) / count($prices);
            }

            $averages[$fuelTypeId] = round($average, 3);
        }

        return response()->json($averages);
    }
}

