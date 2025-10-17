<?php

namespace Database\Seeders;

use App\Models\Vehicle\VehicleHeritage;
use Illuminate\Database\Seeder;

class VehicleHeritageSeeder extends Seeder
{
    public function run(): void
    {
        VehicleHeritage::firstOrCreate(['name' => 'Oficial'], [
            'description' => 'Veículos de propriedade da administração pública, em qualquer esfera de governo, utilizados em serviços de interesse público.'
        ]);

        VehicleHeritage::firstOrCreate(['name' => 'Diplomática'], [
            'description' => 'Veículos de representação de órgãos internacionais, embaixadas e consulados.'
        ]);

        VehicleHeritage::firstOrCreate(['name' => 'De Aluguel/Aprendizagem'], [
            'description' => 'Veículo público pode ter a categoria de aluguel ou de aprendizagem, dependendo de sua finalidade.'
        ]);
    }
}
