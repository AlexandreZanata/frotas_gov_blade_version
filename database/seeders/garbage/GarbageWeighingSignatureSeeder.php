<?php

namespace Database\Seeders\garbage;

use App\Models\garbage\GarbageWeighbridgeOperator;
use App\Models\garbage\GarbageWeighing;
use App\Models\garbage\GarbageWeighingSignature;
use Illuminate\Database\Seeder;

class GarbageWeighingSignatureSeeder extends Seeder
{
    public function run(): void
    {
        $weighing = GarbageWeighing::first();
        $operator = GarbageWeighbridgeOperator::first();

        // Garante que a assinatura digital do usuário operador exista
        if ($weighing && $operator && $operator->user->digitalSignature) {
            GarbageWeighingSignature::updateOrCreate(
                ['garbage_weighing_id' => $weighing->id],
                [
                    'operator_signature_id' => $operator->user->digitalSignature->id,
                    'operator_signed_at' => now(),
                ]
            );
        }
    }
}
