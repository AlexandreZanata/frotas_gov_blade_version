<?php

namespace App\Http\Controllers\runs;

use App\Http\Controllers\Controller;
use App\Models\DigitalSignature;
use App\Models\run\Run;
use App\Models\run\RunSignature;
use App\Models\user\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RunSignatureController extends Controller
{
    /**
     * Assinatura do motorista para uma corrida específica.
     *
     * @param Request $request
     * @param string $runId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signDriver(Request $request, string $runId)
    {
        // CORREÇÃO: Busca o usuário usando o Model importado para garantir o tipo correto
        $user = User::findOrFail(Auth::id());
        $run = Run::where('user_id', $user->id)->findOrFail($runId);

        // Verificar se a corrida já foi assinada pelo motorista
        if ($run->signature && $run->signature->driver_signed_at) {
            return redirect()->back()->with('error', 'Esta corrida já foi assinada por você.');
        }

        // Verificar se a corrida está concluída
        if ($run->status !== 'completed') {
            return redirect()->back()->with('error', 'Apenas corridas concluídas podem ser assinadas.');
        }

        DB::beginTransaction();
        try {
            // Lógica "get or create" para a assinatura digital principal
            $signatureToUse = $this->getOrCreateUserDigitalSignature($user, $request);

            // Usa updateOrCreate para evitar erro caso o admin já tenha criado um registro de assinatura para esta corrida
            RunSignature::updateOrCreate(
                ['run_id' => $run->id],
                [
                    'driver_signature_id' => $signatureToUse->id,
                    'driver_signed_at' => now(),
                ]
            );

            DB::commit();

            return redirect()->route('logbook.index')->with('success', 'Corrida assinada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao assinar corrida {$runId} para usuário {$user->id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Erro ao processar a assinatura: ' . $e->getMessage());
        }
    }

    /**
     * Assina todas as corridas pendentes para o usuário logado.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signAllPending(Request $request)
    {
        // CORREÇÃO: Busca o usuário usando o Model importado para garantir o tipo correto
        $user = User::findOrFail(Auth::id());

        // Encontrar todas as corridas concluídas e não assinadas pelo motorista
        $pendingRuns = Run::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where(function ($query) {
                $query->whereDoesntHave('signature')
                    ->orWhereHas('signature', function ($q) {
                        $q->whereNull('driver_signed_at');
                    });
            })
            ->get();

        if ($pendingRuns->isEmpty()) {
            return redirect()->back()->with('info', 'Nenhuma corrida pendente para assinar.');
        }

        $signedCount = 0;

        DB::beginTransaction();
        try {
            // Lógica "get or create" para a assinatura digital principal, feita uma vez
            $signatureToUse = $this->getOrCreateUserDigitalSignature($user, $request);

            foreach ($pendingRuns as $run) {
                RunSignature::updateOrCreate(
                    ['run_id' => $run->id],
                    [
                        'driver_signature_id' => $signatureToUse->id,
                        'driver_signed_at' => now(),
                    ]
                );
                $signedCount++;
            }
            DB::commit();

            if ($signedCount > 0) {
                return redirect()->back()->with('success', "$signedCount corridas assinadas com sucesso!");
            }

            return redirect()->back()->with('info', 'Nenhuma corrida foi assinada.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao assinar corridas em lote para usuário {$user->id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Ocorreu um erro ao assinar as corridas em lote.');
        }
    }

    /**
     * Assinatura do administrador (se necessário).
     */
    public function signAdmin(Request $request, $runId)
    {
        // Implementar lógica de assinatura do admin conforme necessário
    }

    /**
     * Busca a assinatura digital principal do usuário ou cria uma nova se não existir.
     *
     * @param User $user // CORREÇÃO: O type hint agora corresponde ao model importado
     * @param Request $request
     * @return DigitalSignature
     */
    private function getOrCreateUserDigitalSignature(User $user, Request $request): DigitalSignature
    {
        // Tenta buscar a assinatura principal do usuário
        $digitalSignature = $user->digitalSignature;

        if ($digitalSignature) {
            return $digitalSignature;
        }

        // Se não existir, cria uma nova.
        // O valor do 'signature' do formulário é usado para criar o 'signature_code' na primeira vez.
        $signatureCode = $request->input('signature')
            ? Hash::make($request->input('signature'))
            : Hash::make($user->id . ($user->cpf ?? '') . now()->toString()); // Fallback seguro

        return DigitalSignature::create([
            'user_id' => $user->id,
            'signature_code' => $signatureCode,
        ]);
    }
}

