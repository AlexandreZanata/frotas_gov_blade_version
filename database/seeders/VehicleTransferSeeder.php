<?php
namespace Database\Seeders;
use App\Models\Secretariat;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleTransfer;
use App\Models\VehicleTransferHistory;
use Illuminate\Database\Seeder;

class VehicleTransferSeeder extends Seeder {
    public function run(): void {
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();
        $requester = User::where('email', 'motorista@frotas.gov')->first();
        $origin = Secretariat::where('name', 'Obras')->first();
        $destination = Secretariat::where('name', 'Saúde')->first();

        if ($vehicle && $requester && $origin && $destination) {
            $transfer = VehicleTransfer::create([
                'vehicle_id' => $vehicle->id,
                'origin_secretariat_id' => $origin->id,
                'destination_secretariat_id' => $destination->id,
                'requester_id' => $requester->id,
                'type' => 'temporary',
                'status' => 'pending',

                // --- MODIFICADO AQUI ---
                // 'now()' já retorna um objeto de data e hora completo
                'start_date' => now()->addDay()->setHour(8)->setMinutes(0), // Começa amanhã às 08:00
                'end_date' => now()->addMonth()->setHour(18)->setMinutes(0), // Termina em um mês às 18:00

                'request_notes' => 'Necessário para transporte de equipamentos para o hospital de campanha.',
            ]);

            VehicleTransferHistory::create([
                'vehicle_transfer_id' => $transfer->id,
                'user_id' => $requester->id,
                'status' => 'pending',
                'notes' => 'Solicitação criada pelo sistema.',
            ]);
        }
    }
}
