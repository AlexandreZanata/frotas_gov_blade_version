<?php
namespace Database\Seeders\defect;
use App\Models\defect\DefectReport;
use App\Models\defect\DefectReportAnswer;
use App\Models\defect\DefectReportItem;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Seeder;

class DefectReportSeeder extends Seeder {
    public function run(): void {
        $driver = User::where('email', 'motorista@frotas.gov')->first();
        $vehicle = Vehicle::where('plate', 'BRA2E19')->first();
        $defectItem = DefectReportItem::where('name', 'Freios com barulho ou vibração')->first();

        if ($driver && $vehicle && $defectItem) {
            // Cria a ficha de comunicação de defeito
            $report = DefectReport::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => $driver->id,
                'status' => 'open',
                'notes' => 'O barulho acontece principalmente ao frear em baixa velocidade.',
            ]);

            // Adiciona o item específico ao relatório
            DefectReportAnswer::create([
                'defect_report_id' => $report->id,
                'defect_report_item_id' => $defectItem->id,
                'severity' => 'medium',
                'notes' => 'É um som de assobio agudo vindo da roda dianteira direita.',
            ]);
        }
    }
}
