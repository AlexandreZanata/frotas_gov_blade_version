<?php

namespace Database\Seeders\vehicle;

use App\Models\user\User;
use App\Models\Vehicle\AcquisitionType;
use App\Models\Vehicle\VehiclePriceHistory;
use App\Models\Vehicle\VehiclePriceOrigin;
use Illuminate\Database\Seeder;

class VehiclePriceHistorySeeder extends Seeder
{
    public function run(): void
    {
        $initialAcquisitionType = AcquisitionType::where('name', 'Aquisição Inicial')->first();

        $adminUser = User::where('email', 'admin@frotas.gov')->first();

        $originPrices = VehiclePriceOrigin::with('vehicle')->get();

        if ($initialAcquisitionType && $adminUser) {
            foreach ($originPrices as $origin) {
                VehiclePriceHistory::firstOrCreate(
                    [
                        'vehicle_id' => $origin->vehicle_id,
                        'transactionable_id' => $origin->id,
                        'transactionable_type' => VehiclePriceOrigin::class,
                    ],
                    [
                        'user_id' => $adminUser->id,
                        'acquisition_type_id' => $initialAcquisitionType->id,
                        'amount' => $origin->amount,
                        'description' => 'Valor de aquisição do veículo.',
                        'transaction_date' => $origin->acquisition_date,
                    ]
                );
            }
        }
    }
}
