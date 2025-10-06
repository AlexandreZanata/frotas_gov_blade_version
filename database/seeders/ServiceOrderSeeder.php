<?php
namespace Database\Seeders;
use App\Models\DefectReport;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderItem;
use App\Models\ServiceOrderStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceOrderSeeder extends Seeder {
    public function run(): void {
        $defectReport = DefectReport::first(); // Pega a ficha de defeito criada
        $mechanic = User::where('email', 'admin@frotas.gov')->first(); // Usando admin como mecânico de exemplo

        if ($defectReport && $mechanic) {
            // 1. Cria a Ordem de Serviço a partir da ficha
            $serviceOrder = ServiceOrder::create([
                'defect_report_id' => $defectReport->id,
                'vehicle_id' => $defectReport->vehicle_id,
                'mechanic_id' => $mechanic->id,
                'status' => 'pending_quote',
                'quote_status' => 'draft',
            ]);

            // 2. Cria o primeiro registro no histórico de etapas
            ServiceOrderStatusHistory::create([
                'service_order_id' => $serviceOrder->id,
                'user_id' => $mechanic->id,
                'stage' => 'Diagnóstico Inicial',
                'notes' => 'Serviço aceito pelo mecânico. Iniciando análise do problema.',
            ]);

            // 3. Adiciona itens ao orçamento
            ServiceOrderItem::create([
                'service_order_id' => $serviceOrder->id,
                'description' => 'Pastilhas de Freio Dianteiras (par)',
                'quantity' => 1,
                'unit_price' => 180.50,
            ]);
            ServiceOrderItem::create([
                'service_order_id' => $serviceOrder->id,
                'description' => 'Mão de Obra - Troca de Pastilhas',
                'quantity' => 2, // 2 horas
                'unit_price' => 90.00,
            ]);
        }
    }
}
