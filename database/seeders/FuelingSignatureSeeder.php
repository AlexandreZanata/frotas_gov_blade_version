<?php

namespace Database\Seeders;

use App\Models\DigitalSignature;
use App\Models\fuel\Fueling;
use App\Models\fuel\FuelingSignature;
use Illuminate\Database\Seeder;

class FuelingSignatureSeeder extends Seeder
{
    public function run(): void
    {
        $fueling = Fueling::first(); // Pega o primeiro abastecimento

        if ($fueling) {
            // Encontra o usuário que fez o abastecimento
            $driverUser = $fueling->user;
            // Encontra a assinatura digital desse usuário
            $driverSignature = DigitalSignature::where('user_id', $driverUser->id)->first();

            if ($driverSignature) {
                FuelingSignature::updateOrCreate(
                    ['fueling_id' => $fueling->id],
                    [
                        'driver_signature_id' => $driverSignature->id,
                        'driver_signed_at' => $fueling->created_at ?? now(), // Usa a data do abastecimento
                    ]
                );
            }
        }
    }
}
