<?php

namespace Database\Seeders;

use App\Models\Fine;
use App\Models\FineProcess;
use App\Models\FineSignature;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- BUSCA OS DADOS NECESSÁRIOS ---
        $adminUser = User::where('email', 'admin@frotas.gov')->first();
        $driverUser = User::where('email', 'motorista@frotas.gov')->first();
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();

        // Só executa se encontrar todos os registros
        if ($adminUser && $driverUser && $vehicle) {

            // --- ETAPA 1: CRIA A MULTA ---
            $fine = Fine::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driverUser->id,
                'registered_by_user_id' => $adminUser->id,
                'infraction_code' => '7455-0',
                'description' => 'Transitar em velocidade superior à máxima permitida em até 20%.',
                'location' => 'Av. Principal, em frente ao nº 500',
                'issued_at' => now()->subDays(10),
                'amount' => 130.16,
                'due_date' => now()->addDays(20),
                'status' => 'pending_payment',
            ]);

            // --- ETAPA 2: CRIA O PRIMEIRO REGISTRO NO PROCESSO ---
            FineProcess::create([
                'fine_id' => $fine->id,
                'user_id' => $adminUser->id,
                'stage' => 'Multa Cadastrada no Sistema',
                'notes' => 'Auto de infração A01234567 anexo.',
            ]);

            // --- ETAPA 3: CRIA A ASSINATURA DA MULTA ---
            // Primeiro, busca a assinatura digital que já existe para o motorista
            $driverSignature = $driverUser->digitalSignature;

            if ($driverSignature) {
                // Cria o registro na tabela 'fine_signatures'
                FineSignature::create([
                    'fine_id' => $fine->id,
                    'digital_signature_id' => $driverSignature->id,
                    'signed_at' => now()->subDays(9), // Simula que assinou 1 dia depois
                    'ip_address' => '192.168.1.10',
                ]);

                // Adiciona um segundo passo no processo para registrar a ciência
                FineProcess::create([
                    'fine_id' => $fine->id,
                    'user_id' => $driverUser->id,
                    'stage' => 'Ciência Confirmada pelo Condutor',
                    'notes' => 'Condutor assumiu a responsabilidade pela infração via assinatura digital.',
                ]);
            }
        }
    }
}
