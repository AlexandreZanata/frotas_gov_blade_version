<?php

namespace App\Http\Controllers\garbage;

use App\Http\Controllers\Controller;
use App\Models\garbage\GarbageRun;
use App\Models\garbage\GarbageRunSignature;
use App\Models\DigitalSignature;
use App\Models\user\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GarbageRunSignatureController extends Controller
{
    public function signDriver(Request $request, string $runId)
    {
        $user = User::findOrFail(Auth::id());
        $run = GarbageRun::where('user_id', $user->id)->findOrFail($runId);

        if ($run->signature && $run->signature->driver_signed_at) {
            return redirect()->back()->with('error', 'Esta coleta já foi assinada por você.');
        }

        if ($run->status !== 'completed') {
            return redirect()->back()->with('error', 'Apenas coletas concluídas podem ser assinadas.');
        }

        DB::beginTransaction();
        try {
            $signatureToUse = $this->getOrCreateUserDigitalSignature($user, $request);

            GarbageRunSignature::updateOrCreate(
                ['garbage_run_id' => $run->id],
                [
                    'driver_signature_id' => $signatureToUse->id,
                    'driver_signed_at' => now(),
                ]
            );

            DB::commit();

            return redirect()->route('garbage-logbook.index')->with('success', 'Coleta assinada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao assinar coleta {$runId}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao processar a assinatura.');
        }
    }

    public function signAllPending(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $pendingRuns = GarbageRun::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where(function ($query) {
                $query->whereDoesntHave('signature')
                    ->orWhereHas('signature', function ($q) {
                        $q->whereNull('driver_signed_at');
                    });
            })
            ->get();

        if ($pendingRuns->isEmpty()) {
            return redirect()->back()->with('info', 'Nenhuma coleta pendente para assinar.');
        }

        $signedCount = 0;

        DB::beginTransaction();
        try {
            $signatureToUse = $this->getOrCreateUserDigitalSignature($user, $request);

            foreach ($pendingRuns as $run) {
                GarbageRunSignature::updateOrCreate(
                    ['garbage_run_id' => $run->id],
                    [
                        'driver_signature_id' => $signatureToUse->id,
                        'driver_signed_at' => now(),
                    ]
                );
                $signedCount++;
            }
            DB::commit();

            return redirect()->back()->with('success', "$signedCount coletas assinadas com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao assinar coletas em lote: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao assinar as coletas.');
        }
    }

    private function getOrCreateUserDigitalSignature(User $user, Request $request): DigitalSignature
    {
        $digitalSignature = $user->digitalSignature;

        if ($digitalSignature) {
            return $digitalSignature;
        }

        $signatureCode = $request->input('signature')
            ? Hash::make($request->input('signature'))
            : Hash::make($user->id . ($user->cpf ?? '') . now()->toString());

        return DigitalSignature::create([
            'user_id' => $user->id,
            'signature_code' => $signatureCode,
        ]);
    }
}
