<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\run\Run;
use App\Models\run\RunSignature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminRunSignatureController extends Controller
{
    /**
     * Exibe a página com todas as corridas pendentes de assinatura do administrador.
     */
    public function index(Request $request)
    {
        /** @var User $admin */
        $admin = Auth::user();

        // Query base para corridas que precisam de assinatura de admin
        // A corrida precisa estar 'completed' e já assinada pelo motorista.
        $query = Run::where('status', 'completed')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('driver_signed_at') // Garante que o motorista já assinou
                ->whereNull('admin_signed_at');   // E o admin ainda não
            })
            ->with(['user', 'vehicle.prefix', 'signature.driverSignature.user']);

        // Se for 'sector_manager', filtra apenas usuários da sua secretaria
        if ($admin->hasRole('sector_manager')) {
            $secretariatId = $admin->secretariat_id;
            $query->whereHas('user', function ($q) use ($secretariatId) {
                $q->where('secretariat_id', $secretariatId);
            });
        }
        // 'general_manager' vê tudo, então não aplicamos filtro adicional.

        // Aplica filtros de pesquisa da view
        if ($request->filled('user_name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('user_name') . '%');
            });
        }

        if ($request->filled('vehicle_plate')) {
            $query->whereHas('vehicle', function($q) use ($request) {
                $q->where('plate', 'like', '%' . $request->input('vehicle_plate') . '%');
            });
        }

        $pendingRuns = $query->latest('started_at')->paginate(20)->withQueryString();

        return view('admin.sign-runs', compact('pendingRuns'));
    }

    /**
     * Assina todas as corridas pendentes que o administrador tem permissão para assinar.
     */
    public function signAll(Request $request)
    {
        /** @var User $admin */
        $admin = Auth::user();

        // Busca a assinatura digital do administrador (ou cria uma)
        $adminSignature = $this->getOrCreateUserDigitalSignature($admin, $request);

        // Define a query base para buscar as corridas a serem assinadas
        $query = Run::where('status', 'completed')
            ->whereHas('signature', function ($q) {
                $q->whereNotNull('driver_signed_at')
                    ->whereNull('admin_signed_at');
            });

        // Aplica o filtro de secretaria para 'sector_manager'
        if ($admin->hasRole('sector_manager')) {
            $secretariatId = $admin->secretariat_id;
            $query->whereHas('user', function ($q) use ($secretariatId) {
                $q->where('secretariat_id', $secretariatId);
            });
        }

        // Pega apenas os IDs das corridas para otimizar o update
        $runIdsToSign = $query->pluck('id');

        if ($runIdsToSign->isEmpty()) {
            return redirect()->back()->with('info', 'Nenhuma corrida para assinar.');
        }

        DB::beginTransaction();
        try {
            // Executa a atualização em massa na tabela de assinaturas
            RunSignature::whereIn('run_id', $runIdsToSign)
                ->update([
                    'admin_signature_id' => $adminSignature->id,
                    'admin_signed_at' => now(),
                ]);

            DB::commit();

            return redirect()->back()->with('success', $runIdsToSign->count() . ' corridas foram assinadas com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao assinar corridas em lote pelo admin {$admin->id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao assinar as corridas.');
        }
    }

    /**
     * Busca a assinatura digital principal do usuário ou cria uma nova se não existir.
     */
    private function getOrCreateUserDigitalSignature(User $user, Request $request): \App\Models\DigitalSignature
    {
        $digitalSignature = $user->digitalSignature;

        if ($digitalSignature) {
            return $digitalSignature;
        }

        // Se não existir, cria uma nova.
        $signatureCode = Hash::make($user->id . ($user->cpf ?? '') . now()->toString());

        return \App\Models\DigitalSignature::create([
            'user_id' => $user->id,
            'signature_code' => $signatureCode,
        ]);
    }
}
