<?php

namespace Database\Seeders\maintence;

use App\Models\maintenance\OilChangeSetting;
use App\Models\Vehicle\VehicleCategory;
use Illuminate\Database\Seeder;

class OilChangeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = VehicleCategory::all();

        foreach ($categories as $category) {
            // Definir intervalos padrão baseados na categoria
            $kmInterval = 10000; // 10.000 km por padrão
            $daysInterval = 180; // 6 meses por padrão
            $defaultLiters = null;

            // Ajustar baseado no tipo de veículo
            if (stripos($category->name, 'caminhão') !== false || stripos($category->name, 'ônibus') !== false) {
                $kmInterval = 15000; // Veículos pesados: 15.000 km
                $daysInterval = 120; // 4 meses
                $defaultLiters = 25; // Mais óleo
            } elseif (stripos($category->name, 'moto') !== false) {
                $kmInterval = 5000; // Motos: 5.000 km
                $daysInterval = 180; // 6 meses
                $defaultLiters = 1; // Menos óleo
            } elseif (stripos($category->name, 'carro') !== false || stripos($category->name, 'sedan') !== false) {
                $kmInterval = 10000; // Carros comuns: 10.000 km
                $daysInterval = 180; // 6 meses
                $defaultLiters = 4.5; // Litros padrão
            } elseif (stripos($category->name, 'van') !== false || stripos($category->name, 'utilitário') !== false) {
                $kmInterval = 10000;
                $daysInterval = 150; // 5 meses
                $defaultLiters = 6; // Um pouco mais
            }

            OilChangeSetting::create([
                'vehicle_category_id' => $category->id,
                'km_interval' => $kmInterval,
                'days_interval' => $daysInterval,
                'default_liters' => $defaultLiters,
            ]);
        }
    }
}

