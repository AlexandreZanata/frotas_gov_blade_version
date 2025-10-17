<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Infraction;
use App\Models\InfractionNotice;
use App\Models\FineAttachment;
use App\Models\FineViewLog;
use App\Models\FineProcess;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class FineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $fines = Fine::with(['vehicle', 'driver', 'infractionNotice', 'infractions'])
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('vehicle', fn($q) => $q->where('plate', 'like', "%{$search}%"))
                      ->orWhereHas('driver', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('infractionNotice', fn($q) => $q->where('notice_number', 'like', "%{$search}%"))
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest('issued_at')
            ->paginate(15);

        return view('fines.index', compact('fines', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::orderBy('plate')->get();
        $drivers = User::whereHas('role', fn($q) => $q->where('name', 'driver'))
            ->orderBy('name')
            ->get();

        return view('fines.create', compact('vehicles', 'drivers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'infraction_notice_number' => 'nullable|string',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'driver_id' => 'required|uuid|exists:users,id',
            'location' => 'nullable|string',
            'issued_at' => 'required|date',
            'due_date' => 'nullable|date',
            'infractions' => 'required|array|min:1',
            'infractions.*.code' => 'required|string',
            'infractions.*.description' => 'required|string',
            'infractions.*.base_amount' => 'required|numeric|min:0',
            'infractions.*.extra_fees' => 'nullable|numeric|min:0',
            'infractions.*.discount_amount' => 'nullable|numeric|min:0',
            'infractions.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'infractions.*.points' => 'nullable|integer|min:0',
            'infractions.*.severity' => 'required|in:leve,media,grave,gravissima',
        ]);

        DB::beginTransaction();
        try {
            // Criar ou buscar auto de infração
            $infractionNotice = null;
            if ($request->infraction_notice_number) {
                $infractionNotice = InfractionNotice::firstOrCreate(
                    ['notice_number' => $request->infraction_notice_number],
                    [
                        'issued_at' => $request->issued_at,
                        'issuing_authority' => $request->issuing_authority ?? 'Autoridade Desconhecida'
                    ]
                );
            }

            // Calcular o valor total da multa
            $totalAmount = collect($validated['infractions'])->sum(function($infraction) {
                $base = $infraction['base_amount'];
                $extra = $infraction['extra_fees'] ?? 0;
                $discount = $infraction['discount_amount'] ?? 0;
                return ($base + $extra) - $discount;
            });

            // Criar descrição geral baseada nas infrações
            $generalDescription = collect($validated['infractions'])
                ->pluck('description')
                ->implode('; ');

            // Limitar a descrição se for muito longa
            if (strlen($generalDescription) > 255) {
                $generalDescription = substr($generalDescription, 0, 252) . '...';
            }

            // Criar código de infração concatenado
            $infractionCodes = collect($validated['infractions'])
                ->pluck('code')
                ->implode(', ');

            // Criar multa
            $fine = Fine::create([
                'infraction_notice_id' => $infractionNotice?->id,
                'vehicle_id' => $validated['vehicle_id'],
                'driver_id' => $validated['driver_id'],
                'registered_by_user_id' => auth()->id(),
                'infraction_code' => $infractionCodes,
                'description' => $generalDescription,
                'location' => $validated['location'],
                'issued_at' => $validated['issued_at'],
                'amount' => $totalAmount,
                'due_date' => $validated['due_date'],
                'status' => 'pending_acknowledgement',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Criar infrações
            foreach ($validated['infractions'] as $infractionData) {
                Infraction::create([
                    'fine_id' => $fine->id,
                    'infraction_code' => $infractionData['code'],
                    'description' => $infractionData['description'],
                    'base_amount' => $infractionData['base_amount'],
                    'extra_fees' => $infractionData['extra_fees'] ?? 0,
                    'discount_amount' => $infractionData['discount_amount'] ?? 0,
                    'discount_percentage' => $infractionData['discount_percentage'] ?? 0,
                    'points' => $infractionData['points'] ?? 0,
                    'severity' => $infractionData['severity'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Processar uploads de arquivos
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('fines/attachments', 'public');

                    FineAttachment::create([
                        'fine_id' => $fine->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => 'proof',
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('fines.show', $fine)->with('success', 'Multa cadastrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao cadastrar multa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Fine $fine)
    {
        $fine->load([
            'vehicle',
            'driver',
            'registeredBy',
            'infractionNotice',
            'infractions.attachments',
            'attachments',
            'viewLogs.user',
            'processes.user'
        ]);

        // Registrar visualização
        FineViewLog::create([
            'fine_id' => $fine->id,
            'user_id' => auth()->id(),
            'viewed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Atualizar primeira visualização se necessário
        if (!$fine->first_viewed_at) {
            $fine->update([
                'first_viewed_at' => now(),
                'first_viewed_by' => auth()->id(),
            ]);
        }

        return view('fines.show', compact('fine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fine $fine)
    {
        $fine->load(['infractions', 'infractionNotice']);
        $vehicles = Vehicle::orderBy('plate')->get();
        $drivers = User::whereHas('role', fn($q) => $q->where('name', 'driver'))
            ->orderBy('name')
            ->get();

        return view('fines.edit', compact('fine', 'vehicles', 'drivers'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'infraction_notice_number' => 'nullable|string',
            'issuing_authority' => 'nullable|string',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'driver_id' => 'required|uuid|exists:users,id',
            'location' => 'nullable|string',
            'issued_at' => 'required|date',
            'due_date' => 'nullable|date',
            'description' => 'required|string',
            'status' => 'required|in:pending_acknowledgement,pending_payment,paid,appealed,cancelled',
        ]);

        DB::beginTransaction();
        try {
            // Criar ou buscar auto de infração
            $infractionNotice = null;
            if ($request->infraction_notice_number) {
                $infractionNotice = InfractionNotice::firstOrCreate(
                    ['notice_number' => $request->infraction_notice_number],
                    [
                        'issued_at' => $request->issued_at,
                        'issuing_authority' => $request->issuing_authority ?? 'Autoridade Desconhecida'
                    ]
                );
            }

            // Atualizar multa
            $fine->update([
                'infraction_notice_id' => $infractionNotice?->id,
                'vehicle_id' => $validated['vehicle_id'],
                'driver_id' => $validated['driver_id'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'issued_at' => $validated['issued_at'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

            // Registrar mudança de status se houve alteração
            if ($fine->wasChanged('status')) {
                FineProcess::create([
                    'fine_id' => $fine->id,
                    'user_id' => auth()->id(),
                    'stage' => 'status_change',
                    'notes' => "Status alterado para: {$fine->status_label}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('fines.show', $fine)->with('success', 'Multa atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erro ao atualizar multa: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fine $fine)
    {
        $fine->delete();
        return redirect()->route('fines.index')->with('success', 'Multa excluída com sucesso!');
    }

    public function updateStatus(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending_acknowledgement,pending_payment,paid,appealed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $fine->update(['status' => $validated['status']]);

        FineProcess::create([
            'fine_id' => $fine->id,
            'user_id' => auth()->id(),
            'stage' => 'status_change',
            'notes' => $validated['notes'] ?? "Status alterado para: {$fine->status_label}",
        ]);

        return back()->with('success', 'Status atualizado com sucesso!');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'security_code' => 'required|string',
            'notice_number' => 'required|string',
            'plate' => 'required|string',
        ]);

        $fine = Fine::whereHas('infractionNotice', function($q) use ($validated) {
                $q->where('notice_number', $validated['notice_number'])
                  ->where('security_code', $validated['security_code']);
            })
            ->whereHas('vehicle', fn($q) => $q->where('plate', strtoupper($validated['plate'])))
            ->with(['vehicle', 'driver', 'infractionNotice', 'infractions'])
            ->first();

        if ($fine) {
            return view('fines.verified', compact('fine'));
        }

        return back()->with('error', 'Documento não encontrado ou dados incorretos.');
    }

    public function generatePdf(Fine $fine)
    {
        $fine->load([
            'vehicle',
            'driver',
            'registeredBy',
            'infractionNotice',
            'infractions'
        ]);

        $pdf = Pdf::loadView('fines.pdf', compact('fine'));

        return $pdf->download("multa-{$fine->id}.pdf");
    }

    public function searchVehicles(Request $request)
    {
        $search = $request->get('q');

        $vehicles = Vehicle::where('plate', 'like', "%{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'plate']);

        return response()->json($vehicles);
    }

    public function searchDrivers(Request $request)
    {
        $search = $request->get('q');

        $drivers = User::where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($drivers);
    }

    public function searchInfractionNotices(Request $request)
    {
        $search = $request->get('q');

        $notices = InfractionNotice::where('notice_number', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'notice_number', 'issued_at']);

        return response()->json($notices);
    }
}
