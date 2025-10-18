<?php

namespace App\Http\Controllers\vehicle;

use App\Http\Controllers\Controller;
use App\Models\user\Secretariat;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleTransfer;
use App\Services\VehicleTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VehicleTransferController extends Controller
{
    protected VehicleTransferService $transferService;

    public function __construct(VehicleTransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Exibir lista de transferências
     */
    public function index()
    {
        $user = Auth::user();
        $transfers = $this->transferService->getTransferHistory($user);

        return view('vehicle-transfers.index', compact('transfers'));
    }

    /**
     * Mostrar formulário de nova transferência
     */
    public function create()
    {
        $secretariats = Secretariat::orderBy('name')->get();
        return view('vehicle-transfers.create', compact('secretariats'));
    }

    /**
     * Armazenar nova solicitação de transferência
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'destination_secretariat_id' => 'required|uuid|exists:secretariats,id',
            'type' => ['required', Rule::in(['permanent', 'temporary'])],
            'start_date' => 'required_if:type,temporary|nullable|date|after_or_equal:today',
            'end_date' => 'required_if:type,temporary|nullable|date|after:start_date',
            'request_notes' => 'nullable|string|max:1000',
        ], [
            'vehicle_id.required' => 'Selecione um veículo',
            'vehicle_id.exists' => 'Veículo não encontrado',
            'destination_secretariat_id.required' => 'Selecione a secretaria de destino',
            'destination_secretariat_id.exists' => 'Secretaria não encontrada',
            'type.required' => 'Selecione o tipo de transferência',
            'type.in' => 'Tipo de transferência inválido',
            'start_date.required_if' => 'Data de início é obrigatória para empréstimos temporários',
            'start_date.after_or_equal' => 'Data de início não pode ser no passado',
            'end_date.required_if' => 'Data de término é obrigatória para empréstimos temporários',
            'end_date.after' => 'Data de término deve ser posterior à data de início',
        ]);

        try {
            $transfer = $this->transferService->createTransferRequest($validated, Auth::user());

            $message = $transfer->isApproved()
                ? 'Transferência realizada com sucesso!'
                : 'Solicitação de transferência enviada com sucesso! Aguarde aprovação.';

            return redirect()->route('vehicle-transfers.show', $transfer)
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao criar transferência: ' . $e->getMessage());
        }
    }

    /**
     * Exibir detalhes de uma transferência
     */
    public function show(VehicleTransfer $vehicleTransfer)
    {
        $vehicleTransfer->load(['vehicle.prefix', 'originSecretariat', 'destinationSecretariat', 'requester', 'approver']);

        return view('vehicle-transfers.show', compact('vehicleTransfer'));
    }

    /**
     * Mostrar transferências pendentes para aprovação
     */
    public function pending()
    {
        $user = Auth::user();
        $pendingTransfers = $this->transferService->getPendingTransfersForApproval($user);

        return view('vehicle-transfers.pending', compact('pendingTransfers'));
    }

    /**
     * Aprovar transferência
     */
    public function approve(Request $request, VehicleTransfer $vehicleTransfer)
    {
        $validated = $request->validate([
            'approver_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->transferService->approveTransfer(
                $vehicleTransfer,
                Auth::user(),
                $validated['approver_notes'] ?? null
            );

            return redirect()->route('vehicle-transfers.show', $vehicleTransfer)
                ->with('success', 'Transferência aprovada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Rejeitar transferência
     */
    public function reject(Request $request, VehicleTransfer $vehicleTransfer)
    {
        $validated = $request->validate([
            'approver_notes' => 'required|string|max:1000',
        ], [
            'approver_notes.required' => 'Informe o motivo da rejeição',
        ]);

        try {
            $this->transferService->rejectTransfer(
                $vehicleTransfer,
                Auth::user(),
                $validated['approver_notes']
            );

            return redirect()->route('vehicle-transfers.show', $vehicleTransfer)
                ->with('success', 'Transferência rejeitada.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mostrar transferências ativas (para devolução)
     */
    public function active()
    {
        $user = Auth::user();
        $activeTransfers = $this->transferService->getActiveTransfersForReturn($user);

        return view('vehicle-transfers.active', compact('activeTransfers'));
    }

    /**
     * Devolver veículo
     */
    public function return(Request $request, VehicleTransfer $vehicleTransfer)
    {
        $validated = $request->validate([
            'return_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->transferService->returnVehicle(
                $vehicleTransfer,
                Auth::user(),
                $validated['return_notes'] ?? null
            );

            return redirect()->route('vehicle-transfers.show', $vehicleTransfer)
                ->with('success', 'Veículo devolvido com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * API: Buscar veículo por placa ou prefixo
     */
    public function searchVehicle(Request $request)
    {
        $search = $request->input('search');

        if (empty($search)) {
            return response()->json([]);
        }

        $vehicles = Vehicle::with(['prefix', 'secretariat'])
            ->where(function ($query) use ($search) {
                $query->where('plate', 'like', "%{$search}%")
                      ->orWhereHas('prefix', function ($q) use ($search) {
                          $q->where('abbreviation', 'like', "%{$search}%");
                      });
            })
            ->limit(10)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'plate' => $vehicle->plate,
                    'prefix' => $vehicle->prefix?->abbreviation,
                    'name' => $vehicle->name,
                    'model' => $vehicle->model,
                    'secretariat_id' => $vehicle->secretariat_id,
                    'secretariat_name' => $vehicle->secretariat?->name,
                    'display' => $vehicle->prefix
                        ? "{$vehicle->prefix->abbreviation} - {$vehicle->plate} - {$vehicle->name}"
                        : "{$vehicle->plate} - {$vehicle->name}",
                ];
            });

        return response()->json($vehicles);
    }
}

