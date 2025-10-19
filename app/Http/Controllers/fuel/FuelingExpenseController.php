<?php

namespace App\Http\Controllers\fuel;

use App\Http\Controllers\Controller;
use App\Models\fuel\Fueling;
use App\Models\fuel\FuelingGasStationExpense;
use App\Models\fuel\FuelingVehicleExpense;
use App\Models\fuel\FuelingViewLog;
use App\Models\Vehicle\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\fuel\FuelingSignature;
use App\Models\DigitalSignature;

class FuelingExpenseController extends Controller
{
    /**
     * Exibe um resumo dos gastos totais por veículo e por posto.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $vehicleExpensesQuery = FuelingVehicleExpense::with(['vehicle', 'vehicle.secretariat']);
        $gasStationExpenses = null;
        $totalExpenses = 0;

        // === VERIFICAÇÃO DE ACESSO SIMPLIFICADA ===
        if (!$this->userHasAccess($user)) {
            abort(403, 'Acesso negado. Você não possui privilégios suficientes.');
        }

        // === LÓGICA DE PERMISSÃO ===
        if ($user->hasRole('general_manager') || $user->role_id === '0199fdbc-9f2a-71fe-907e-5c23fdbb5eb5') {
            // GESTOR GERAL: Vê os gastos de TODOS os postos
            $gasStationExpenses = FuelingGasStationExpense::with('gasStation')
                ->orderBy('total_fuel_cost', 'desc')
                ->paginate(15, ['*'], 'stationsPage');

            // CORREÇÃO: Calcular o total geral SOMANDO TODOS os gastos
            $totalExpenses = Fueling::sum(DB::raw('liters * value_per_liter'));

        } elseif ($user->hasRole('sector_manager') || $user->role_id === '0199fdbc-9f2f-7260-b10a-17860c6602ee') {
            // GESTOR SETORIAL: Vê APENAS veículos da sua secretaria
            $secretariatId = $user->secretariat_id;

            $vehicleIds = Vehicle::where('secretariat_id', $secretariatId)->pluck('id');
            $vehicleExpensesQuery->whereIn('vehicle_id', $vehicleIds);

            // CORREÇÃO: Calcular o total geral APENAS para veículos da secretaria
            $totalExpenses = Fueling::whereIn('vehicle_id', $vehicleIds)
                ->sum(DB::raw('liters * value_per_liter'));
        }

        // Executa a query de despesas de veículos
        $vehicleExpenses = $vehicleExpensesQuery->orderBy('total_fuel_cost', 'desc')
            ->paginate(15, ['*'], 'vehiclesPage');

        return view('fueling_expenses.index', compact(
            'vehicleExpenses',
            'gasStationExpenses',
            'totalExpenses'
        ));
    }

    /**
     * Exibe os detalhes de abastecimentos para um veículo específico.
     */
    public function showVehicleFuelings(Request $request, string $vehicleId)
    {
        $user = Auth::user();
        $vehicle = Vehicle::with('secretariat')->findOrFail($vehicleId);

        // Verifica acesso
        if (!$this->userHasAccess($user)) {
            abort(403, 'Acesso negado.');
        }

        // Verifica permissão específica para o veículo
        if (($user->hasRole('sector_manager') || $user->role_id === '0199fdbc-9f2f-7260-b10a-17860c6602ee') &&
            $vehicle->secretariat_id != $user->secretariat_id) {
            abort(403, 'Acesso não autorizado a este veículo.');
        }

        // Query corrigida com ordenação por assinatura
        $fuelings = Fueling::where('vehicle_id', $vehicleId)
            ->with(['user', 'fuelType', 'gasStation', 'signature'])
            ->select('fuelings.*')
            ->leftJoin('fuelings_signatures', 'fuelings.id', '=', 'fuelings_signatures.fueling_id')
            ->orderByRaw('fuelings_signatures.admin_signature_id IS NULL DESC')
            ->orderBy('fuelings.fueled_at', 'desc')
            ->paginate(20);

        // Busca o total gasto pelo veículo - CORREÇÃO AQUI
        $vehicleExpense = FuelingVehicleExpense::where('vehicle_id', $vehicleId)->first();

        // Se não existir registro, calcula manualmente
        if (!$vehicleExpense) {
            $totalFuelCost = Fueling::where('vehicle_id', $vehicleId)
                ->sum(DB::raw('liters * value_per_liter'));
            $vehicleExpense = (object) ['total_fuel_cost' => $totalFuelCost];
        }

        return view('fueling_expenses.vehicle_details', compact('fuelings', 'vehicleExpense', 'vehicle'));
    }

    /**
     * Exibe os detalhes de abastecimentos para um posto específico.
     */
    public function showGasStationFuelings(Request $request, string $gasStationId)
    {
        $user = Auth::user();

        // Verifica acesso
        if (!$this->userHasAccess($user)) {
            abort(403, 'Acesso negado.');
        }

        // Apenas o Gestor Geral pode ver detalhes por posto
        if (!$user->hasRole('general_manager') && $user->role_id !== '0199fdbc-9f2a-71fe-907e-5c23fdbb5eb5') {
            abort(403, 'Acesso não autorizado. Apenas gestores gerais podem visualizar detalhes de postos.');
        }

        // Query corrigida com ordenação por assinatura
        $fuelings = Fueling::where('gas_station_id', $gasStationId)
            ->with(['user', 'fuelType', 'vehicle', 'vehicle.secretariat', 'signature'])
            ->select('fuelings.*')
            ->leftJoin('fuelings_signatures', 'fuelings.id', '=', 'fuelings_signatures.fueling_id')
            ->orderByRaw('fuelings_signatures.admin_signature_id IS NULL DESC')
            ->orderBy('fuelings.fueled_at', 'desc')
            ->paginate(20);

        $gasStationExpense = FuelingGasStationExpense::where('gas_station_id', $gasStationId)->first();

        return view('fueling_expenses.station_details', compact('fuelings', 'gasStationExpense'));
    }

    /**
     * Exibe um registro de abastecimento específico e registra a visualização.
     */
    public function showFuelingDetail(Request $request, string $fuelingId)
    {
        $user = Auth::user();
        $fueling = Fueling::with(['user', 'vehicle', 'vehicle.secretariat', 'fuelType', 'gasStation'])->findOrFail($fuelingId);

        // Verifica acesso
        if (!$this->userHasAccess($user)) {
            abort(403, 'Acesso negado.');
        }

        // Se o usuário for Gestor Setorial, verifica se o veículo pertence à sua secretaria
        if (($user->hasRole('sector_manager') || $user->role_id === '0199fdbc-9f2f-7260-b10a-17860c6602ee') &&
            $fueling->vehicle->secretariat_id != $user->secretariat_id) {
            abort(403, 'Acesso não autorizado a este registro.');
        }

        // Registra o log de visualização
        FuelingViewLog::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'fueling_id' => $fueling->id,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Recupera os logs de visualização
        $viewLogs = FuelingViewLog::where('fueling_id', $fueling->id)
            ->with('user')
            ->orderBy('viewed_at', 'desc')
            ->get();

        return view('fueling_expenses.fueling_detail', compact('fueling', 'viewLogs'));
    }

    /**
     * Retorna dados de previsão de gastos usando Regressão Linear Simples.
     */
    public function getExpenseForecast(Request $request)
    {
        $user = Auth::user();

        // Verifica acesso
        if (!$this->userHasAccess($user)) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        $historicalData = null;
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

        // Query de dados históricos
        $query = Fueling::query()
            ->select(
                DB::raw('SUM(liters * value_per_liter) as total_cost'),
                DB::raw("DATE_FORMAT(fueled_at, '%Y-%m') as month")
            )
            ->where('fueled_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        // Aplicar filtro de permissão
        if ($user->hasRole('sector_manager') || $user->role_id === '0199fdbc-9f2f-7260-b10a-17860c6602ee') {
            $secretariatId = $user->secretariat_id;
            $vehicleIds = Vehicle::where('secretariat_id', $secretariatId)->pluck('id');
            $query->whereIn('vehicle_id', $vehicleIds);
        }

        $historicalData = $query->get();

        // Verifica se há dados suficientes
        if ($historicalData->count() < 3) {
            return response()->json([
                'error' => 'Dados históricos insuficientes para gerar uma previsão confiável (mínimo 3 meses).',
                'historical' => $historicalData,
            ], 400);
        }

        // Mapeia os dados para (x, y)
        $points = $historicalData->map(function ($item, $index) {
            return [
                'x' => $index,
                'y' => (float)$item->total_cost,
                'label' => $item->month,
            ];
        });

        // Calcula a Regressão Linear
        try {
            $regression = $this->calculateLinearRegression($points->all());
            $m = $regression['m'];
            $b = $regression['b'];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // Gera a Previsão
        $forecast = [];
        $lastMonthIndex = $points->last()['x'];
        $lastMonthLabel = Carbon::createFromFormat('Y-m', $points->last()['label']);

        for ($i = 1; $i <= 6; $i++) {
            $nextIndex = $lastMonthIndex + $i;
            $nextLabelDate = $lastMonthLabel->copy()->addMonths($i);
            $predictedCost = ($m * $nextIndex) + $b;

            $forecast[] = [
                'label' => $nextLabelDate->format('Y-m'),
                'predicted_cost' => round($predictedCost, 2),
                'x' => $nextIndex
            ];
        }

        // Formata dados históricos para o gráfico
        $trendLine = $points->map(function($point) use ($m, $b) {
            return [
                'label' => $point['label'],
                'actual_cost' => $point['y'],
                'trend_cost' => round(($m * $point['x']) + $b, 2)
            ];
        });

        return response()->json([
            'historical_trend' => $trendLine,
            'forecast_next_6_months' => $forecast,
            'analysis' => [
                'trend_slope' => $m,
                'trend_base' => $b,
                'trend_direction' => $m > 0 ? 'aumentando' : ($m < 0 ? 'diminuindo' : 'estável'),
                'monthly_change_value' => round(abs($m), 2)
            ]
        ]);
    }

    /**
     * Função auxiliar para calcular a Regressão Linear Simples.
     */
    private function calculateLinearRegression(array $points): array
    {
        $n = count($points);
        if ($n == 0) {
            throw new \Exception("Nenhum ponto de dado fornecido para regressão.");
        }

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($points as $point) {
            $x = $point['x'];
            $y = $point['y'];

            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumX2 += ($x * $x);
        }

        $denominator = ($n * $sumX2) - ($sumX * $sumX);
        if ($denominator == 0) {
            return ['m' => 0, 'b' => $sumY / $n];
        }

        $m = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
        $b = ($sumY - ($m * $sumX)) / $n;

        return ['m' => $m, 'b' => $b];
    }

    /**
     * Verifica se o usuário tem acesso ao módulo de gastos.
     * Método flexível que funciona com diferentes sistemas de roles.
     */
    private function userHasAccess($user): bool
    {
        // Verifica se o usuário tem role de gestor (usando role_id ou relação de roles)
        $isGeneralManager = $user->hasRole('general_manager') ||
            $user->role_id === '0199fdbc-9f2a-71fe-907e-5c23fdbb5eb5';

        $isSectorManager = $user->hasRole('sector_manager') ||
            $user->role_id === '0199fdbc-9f2f-7260-b10a-17860c6602ee';

        return $isGeneralManager || $isSectorManager;
    }

    /**
     * Assina um abastecimento (CORRIGIDO para o erro do signature_code)
     */
    public function signFueling(Request $request, string $fuelingId)
    {
        $user = Auth::user();

        // Verifica acesso
        if (!$this->userHasAccess($user)) {
            return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
        }

        $fueling = Fueling::with('vehicle')->findOrFail($fuelingId);

        // Verifica se o gestor setorial só está tentando assinar abastecimentos da sua secretaria
        if (($user->hasRole('sector_manager') || $user->role_id === '0199fdbc-9f2f-7260-b10a-17860c6602ee') &&
            $fueling->vehicle->secretariat_id != $user->secretariat_id) {
            return response()->json(['success' => false, 'message' => 'Acesso não autorizado a este registro.'], 403);
        }

        try {
            // Verifica se já existe uma assinatura
            $existingSignature = FuelingSignature::where('fueling_id', $fuelingId)->first();

            if ($existingSignature && $existingSignature->admin_signature_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este abastecimento já foi assinado.'
                ], 400);
            }

            // CORREÇÃO: Cria a assinatura digital sem o campo signature_code que não existe
            $adminSignature = DigitalSignature::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'signature_data' => "Assinado por {$user->name} em " . now()->format('d/m/Y H:i'),
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cria ou atualiza o registro de assinatura do abastecimento
            if ($existingSignature) {
                $existingSignature->update([
                    'admin_signature_id' => $adminSignature->id,
                    'admin_signed_at' => now(),
                ]);
            } else {
                FuelingSignature::create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'fueling_id' => $fuelingId,
                    'admin_signature_id' => $adminSignature->id,
                    'admin_signed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Abastecimento assinado com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao assinar abastecimento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao assinar abastecimento: ' . $e->getMessage()
            ], 500);
        }
    }
}
