<?php

namespace Database\Seeders;

use App\Models\GarbageWeighbridgeOperator;
use App\Models\GarbageWeighing;
use App\Models\GarbageWeighingSignature;
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
