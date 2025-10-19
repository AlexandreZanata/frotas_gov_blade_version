<?php
namespace Database\Seeders\fuel;

use App\Models\fuel\Fueling;
use App\Models\user\User; // Ajustei o namespace baseado no seu código anterior
use App\Models\Vehicle\Vehicle; // Ajustei o namespace baseado nos seus arquivos blade
use App\Models\fuel\FuelType;
use App\Models\fuel\GasStation;
use App\Models\run\Run; // Ajustei o namespace
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FuelingSeeder extends Seeder
{
    public function run(): void
    {
        // Usei o email do admin como exemplo, já que 'motorista@frotas.gov' foi criado no ChatSeeder
        // e 'motorista.saude@frotas.gov' não existe nos seeders fornecidos. Ajuste se necessário.
        $driver = User::where('email', 'admin@frotas.gov')->first();
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();
        $fuelType = FuelType::where('name', 'Diesel S10')->first();
        $gasStation = GasStation::where('name', 'Posto Central')->first();

        // Tenta encontrar o posto "Abastecimento Manual", cria se não existir
        $manualGasStation = GasStation::firstOrCreate(
            ['name' => 'Abastecimento Manual'],
            [
                // Você pode adicionar outros campos padrão se necessário
                'address' => 'N/A',
                'status' => 'active',
            ]
        );

        $run = Run::first(); // Pega a primeira corrida existente

        // Abastecimento credenciado
        if ($driver && $vehicle && $fuelType && $gasStation) {
            $liters = 50.500;
            $value_per_liter_example = 5.89;
            $total_value = round($liters * $value_per_liter_example, 2);

            Fueling::updateOrCreate(
                [
                    // Usar um campo realmente único para buscar ou criar
                    // 'public_code' pode ser gerado aleatoriamente, usar 'id' se for fixo
                    'id' => '0199f4c2-c87b-714c-861a-9d45bc2d306d', // Use ID se for fixo
                ],
                [
                    'user_id' => $driver->id,
                    'vehicle_id' => $vehicle->id,
                    'run_id' => $run ? $run->id : null,
                    'fuel_type_id' => $fuelType->id,
                    'gas_station_id' => $gasStation->id,
                    'gas_station_name' => null, // No credenciado, não usa nome manual
                    'fueled_at' => now(),
                    'km' => 15230,
                    'liters' => $liters,
                    'value' => $total_value, // Coluna 'value' deve existir
                    'value_per_liter' => $value_per_liter_example, // Coluna 'value_per_liter'
                    'invoice_path' => 'invoices/sample.pdf',
                    'public_code' => 'ABS-20251017-ESCG4F', // Certifique-se que é único ou gere aleatório
                    // REMOVIDO: 'signature_id' => null,
                    // REMOVIDO: 'viewed_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        } else {
            $this->command->warn('Não foi possível criar o abastecimento credenciado. Verifique se o motorista, veículo, tipo de combustível e posto existem.');
        }

        // Abastecimento manual de exemplo
        if ($driver && $vehicle && $fuelType && $manualGasStation) {
            Fueling::updateOrCreate(
                [
                    // Usar um campo realmente único para buscar ou criar
                    'public_code' => 'ABS-20251017-MANUAL1', // Garanta que seja único
                ],
                [
                    'id' => Str::uuid(), // Gera um novo UUID se for criar
                    'user_id' => $driver->id,
                    'vehicle_id' => $vehicle->id,
                    'run_id' => $run ? $run->id : null,
                    'fuel_type_id' => $fuelType->id,
                    'gas_station_id' => $manualGasStation->id, // ID do posto manual
                    'gas_station_name' => 'Posto do Zé', // Nome específico informado pelo usuário
                    'fueled_at' => now()->subDays(2),
                    'km' => 15100,
                    'liters' => 35.000,
                    'value' => 182.00, // Coluna 'value'
                    'value_per_liter' => 5.20, // Coluna 'value_per_liter'
                    'invoice_path' => null,
                    // REMOVIDO: 'signature_id' => null,
                    // REMOVIDO: 'viewed_by' => null,
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ]
            );
        } else {
            $this->command->warn('Não foi possível criar o abastecimento manual. Verifique se o motorista, veículo, tipo de combustível e posto manual existem.');
        }
    }
}
