<?php
namespace Database\Seeders;
use App\Models\Run;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
class RunSeeder extends Seeder
{
    public function run(): void
    {
        $driver = User::where('email', 'admin@frotas.gov')->first(); // Usando o admin como exemplo
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();

        if ($driver && $vehicle) {
            Run::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => $driver->id,
                'start_km' => 15000,
                'started_at' => now(),
                'destination' => 'Hospital Central',
                'status' => 'in_progress',
            ]);
        }
    }
}
