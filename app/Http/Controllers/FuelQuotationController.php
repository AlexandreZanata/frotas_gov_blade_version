<?php

namespace App\Http\Controllers;

use App\Models\fuel\FuelPumpPrice;
use App\Models\fuel\FuelQuotation;
use App\Models\fuel\FuelQuotationDiscount;
use App\Models\fuel\FuelQuotationPrice;
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
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
            'notes' => 'nullable|string',

            // Nova estrutura: preços por posto (cada posto tem todos os combustíveis)
            'stations' => 'required|array|min:1',
            'stations.*.gas_station_id' => 'required|exists:gas_stations,id',
            'stations.*.prices' => 'required|array',
            'stations.*.prices.*.fuel_type_id' => 'required|exists:fuel_types,id',
            'stations.*.prices.*.price' => 'nullable|numeric|min:0',
            'stations.*.prices.*.image_1' => 'nullable|image|max:5120',
            'stations.*.prices.*.image_2' => 'nullable|image|max:5120',

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
                'calculation_method' => 'simple_average',
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // Salvar preços por posto
            foreach ($request->stations as $stationData) {
                foreach ($stationData['prices'] as $priceData) {
                    // Ignorar se preço não foi informado ou é 0
                    if (empty($priceData['price']) || $priceData['price'] == 0) {
                        continue;
                    }

                    $image1Path = null;
                    $image2Path = null;

                    if (isset($priceData['image_1'])) {
                        $image1Path = $priceData['image_1']->store('fuel-quotations/images', 'public');
                    }

                    if (isset($priceData['image_2'])) {
                        $image2Path = $priceData['image_2']->store('fuel-quotations/images', 'public');
                    }

                    FuelQuotationPrice::create([
                        'fuel_quotation_id' => $quotation->id,
                        'gas_station_id' => $stationData['gas_station_id'],
                        'fuel_type_id' => $priceData['fuel_type_id'],
                        'price' => $priceData['price'],
                        'image_1' => $image1Path,
                        'image_2' => $image2Path,
                    ]);
                }
            }

            // Salvar preços de bomba (se fornecidos)
            if ($request->has('pump_prices') && is_array($request->pump_prices)) {
                foreach ($request->pump_prices as $pumpData) {
                    $evidencePath = null;

                    if (isset($pumpData['evidence']) && $pumpData['evidence']) {
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

            // Calcular médias e aplicar descontos usando configurações
            $this->calculateAndApplyDiscounts($quotation);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cotação criada com sucesso!',
                'redirect' => route('fuel-quotations.show', $quotation)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar cotação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcular médias e aplicar descontos baseados nas configurações
     */
    private function calculateAndApplyDiscounts(FuelQuotation $quotation)
    {
        $fuelTypes = FuelType::with(['calculationMethods' => function($query) {
            $query->where('is_active', true)->orderBy('order');
        }, 'discountSettings' => function($query) {
            $query->where('is_active', true)->orderBy('order');
        }])->get();

        foreach ($fuelTypes as $fuelType) {
            // Calcular média de preços para este tipo de combustível
            $prices = $quotation->prices()
                ->where('fuel_type_id', $fuelType->id)
                ->pluck('price');

            if ($prices->isEmpty()) {
                continue;
            }

            // Usar método de cálculo configurado ou média simples
            $calculationMethod = $fuelType->calculationMethods->first();

            if ($calculationMethod && $calculationMethod->calculation_type === 'weighted_average') {
                // Implementar média ponderada se necessário
                $averagePrice = $prices->avg();
            } else {
                $averagePrice = $prices->avg();
            }

            // Aplicar descontos configurados
            $discountSetting = $fuelType->discountSettings->first();
            $discountPercentage = 0;
            $finalPrice = $averagePrice;

            if ($discountSetting) {
                if ($discountSetting->discount_type === 'percentage') {
                    $discountPercentage = $discountSetting->percentage;
                    $finalPrice = $averagePrice - ($averagePrice * ($discountPercentage / 100));
                } elseif ($discountSetting->discount_type === 'fixed') {
                    $finalPrice = $averagePrice - $discountSetting->fixed_value;
                    $discountPercentage = (($averagePrice - $finalPrice) / $averagePrice) * 100;
                }
            }

            FuelQuotationDiscount::create([
                'fuel_quotation_id' => $quotation->id,
                'fuel_type_id' => $fuelType->id,
                'average_price' => round($averagePrice, 3),
                'discount_percentage' => round($discountPercentage, 2),
                'final_price' => round($finalPrice, 3),
            ]);
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
            if ($request->input('method') === 'simple_average') {
                $average = array_sum($prices) / count($prices);
            } else {
                // Método personalizado
                $average = array_sum($prices) / count($prices);
            }

            $averages[$fuelTypeId] = round($average, 3);
        }

        return response()->json($averages);
    }

    /**
     * Deletar imagem de um preço
     */
    public function deleteImage(Request $request)
    {
        $request->validate([
            'price_id' => 'required|exists:fuel_quotation_prices,id',
            'image_field' => 'required|in:image_1,image_2',
        ]);

        try {
            $price = FuelQuotationPrice::findOrFail($request->price_id);
            $imageField = $request->image_field;

            if ($price->$imageField) {
                Storage::disk('public')->delete($price->$imageField);
                $price->$imageField = null;
                $price->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagem removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover imagem: ' . $e->getMessage()
            ], 500);
        }
    }
}
