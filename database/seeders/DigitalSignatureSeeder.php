<?php

namespace Database\Seeders;

use App\Models\DigitalSignature;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DigitalSignatureSeeder extends Seeder
{
    public function run(): void
    {
        // Pega o usuário admin/motorista que cria a corrida no RunSeeder
        $driverUser = User::where('email', 'admin@frotas.gov')->first();

        if ($driverUser) {
            // Cria ou atualiza a assinatura para este usuário
            DigitalSignature::updateOrCreate(
                ['user_id' => $driverUser->id],
                [
                    // Em um app real, isso seria uma chave criptográfica.
                    // Usamos um hash do ID do usuário como exemplo seguro.
                    'signature_code' => Hash::make($driverUser->id . '-secret-signature-key'),
                ]
            );
        }
    }
}
