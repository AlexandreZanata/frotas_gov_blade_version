<?php

namespace Database\Seeders\fuel;

use App\Models\fuel\GasStation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GasStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [
            [
                'id' => '0199f999-9999-9999-9999-999999999999', // ID fixo para abastecimento manual
                'name' => 'Abastecimento Manual',
                'address' => 'N/A',
                'cnpj' => '00.000.000/0000-00',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Posto Central',
                'address' => 'Av. Brasil, 123, Centro',
                'cnpj' => '11.111.111/0001-11',
                'status' => 'active',
            ],
            [
                'name' => 'Posto Petrobras - Saída Sul',
                'address' => 'Rod. BR-101, Km 50',
                'cnpj' => '22.222.222/0001-22',
                'status' => 'active',
            ],
            [
                'name' => 'Posto Ipiranga - Bairro Norte',
                'address' => 'Rua das Flores, 456',
                'cnpj' => '33.333.333/0001-33',
                'status' => 'inactive',
            ],
        ];

        foreach ($stations as $station) {
            GasStation::updateOrCreate(
                ['id' => $station['id'] ?? Str::uuid()],
                $station
            );
        }
    }
}
