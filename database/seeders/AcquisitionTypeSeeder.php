<?php

namespace Database\Seeders;

use App\Models\Vehicle\AcquisitionType;
use Illuminate\Database\Seeder;

class AcquisitionTypeSeeder extends Seeder
{
    public function run(): void
    {
        AcquisitionType::firstOrCreate(['name' => 'Aquisição Inicial']);
        AcquisitionType::firstOrCreate(['name' => 'Compra por Licitação']);
        AcquisitionType::firstOrCreate(['name' => 'Doação']);
        AcquisitionType::firstOrCreate(['name' => 'Compra Direta']);

        AcquisitionType::firstOrCreate(['name' => 'Serviço de Manutenção']);
        AcquisitionType::firstOrCreate(['name' => 'Multa de Trânsito']);
        AcquisitionType::firstOrCreate(['name' => 'Abastecimento']);
        AcquisitionType::firstOrCreate(['name' => 'Outros']);
    }
}
