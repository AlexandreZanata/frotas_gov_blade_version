<?php

namespace App\Services;

use App\Models\BackupReport;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\Prefix;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class BackupPdfService
{
    public function generateVehicleBackup(Vehicle $vehicle): BackupReport
    {
        $vehicle->load(['category', 'prefix', 'status', 'fuelType']);

        // Buscar dados relacionados
        $serviceOrders = $vehicle->serviceOrders()->with('items', 'user')->get();
        $fuelings = $vehicle->fuelings()->with('user', 'gasStation')->get();
        $runs = $vehicle->runs()->with('user', 'signatures')->get();
        $defectReports = $vehicle->defectReports()->with('user', 'items')->get();
        $fines = $vehicle->fines()->with('user')->get();
        $transfers = $vehicle->transfers()->with('fromSecretariat', 'toSecretariat', 'user')->get();

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        // Configurações do PDF
        $pdf->SetCreator('Sistema de Frotas');
        $pdf->SetAuthor(auth()->user()->name ?? 'Sistema');
        $pdf->SetTitle('Backup - Veículo ' . $vehicle->plate);
        $pdf->SetSubject('Backup de dados do veículo');

        // Remove header/footer padrão
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 10, 'BACKUP DE DADOS DO VEÍCULO', 0, 1, 'C');
        $pdf->Ln(5);

        // Informações do veículo
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetFillColor(59, 130, 246);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Informações do Veículo', 0, 1, 'L', true);
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(31, 41, 55);

        $vehicleData = [
            'Nome' => $vehicle->name,
            'Marca' => $vehicle->brand,
            'Placa' => strtoupper($vehicle->plate),
            'Ano/Modelo' => $vehicle->model_year,
            'Chassi' => $vehicle->chassis ?: '-',
            'RENAVAM' => $vehicle->renavam ?: '-',
            'Registro' => $vehicle->registration ?: '-',
            'Categoria' => $vehicle->category->name ?? '-',
            'Prefixo' => $vehicle->prefix->name ?? '-',
            'Combustível' => $vehicle->fuelType->name ?? '-',
            'Capacidade do Tanque' => $vehicle->fuel_tank_capacity . ' L',
            'Status' => $vehicle->status->name ?? '-',
        ];

        foreach ($vehicleData as $label => $value) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(50, 6, $label . ':', 0, 0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(0, 6, $value, 0, 1);
        }

        // Ordens de Serviço
        if ($serviceOrders->count() > 0) {
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Ordens de Serviço (' . $serviceOrders->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetTextColor(31, 41, 55);
            foreach ($serviceOrders as $so) {
                // Verifica se precisa de nova página
                if ($pdf->GetY() > 250) {
                    $pdf->AddPage();
                }

                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->Cell(0, 6, 'OS #' . substr($so->id, 0, 8) . ' - ' . $so->created_at->format('d/m/Y'), 0, 1);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->MultiCell(0, 5, 'Descrição: ' . ($so->description ?? '-'), 0, 'L');
                $pdf->Ln(2);
            }
        }

        // Abastecimentos
        if ($fuelings->count() > 0) {
            if ($pdf->GetY() > 230) {
                $pdf->AddPage();
            }

            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Abastecimentos (' . $fuelings->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(31, 41, 55);
            foreach ($fuelings as $fuel) {
                if ($pdf->GetY() > 270) {
                    $pdf->AddPage();
                }

                $pdf->Cell(40, 5, $fuel->created_at->format('d/m/Y'), 0, 0);
                $pdf->Cell(40, 5, $fuel->liters . ' L', 0, 0);
                $pdf->Cell(0, 5, 'R$ ' . number_format($fuel->total_price ?? 0, 2, ',', '.'), 0, 1);
            }
        }

        // Viagens
        if ($runs->count() > 0) {
            if ($pdf->GetY() > 230) {
                $pdf->AddPage();
            }

            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Viagens (' . $runs->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(31, 41, 55);
            foreach ($runs as $run) {
                if ($pdf->GetY() > 270) {
                    $pdf->AddPage();
                }

                $pdf->MultiCell(0, 5, 'Data: ' . $run->created_at->format('d/m/Y') . ' | Destino: ' . ($run->destination ?? '-'), 0, 'L');
            }
        }

        // Multas
        if ($fines->count() > 0) {
            if ($pdf->GetY() > 230) {
                $pdf->AddPage();
            }

            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Multas (' . $fines->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(31, 41, 55);
            foreach ($fines as $fine) {
                if ($pdf->GetY() > 270) {
                    $pdf->AddPage();
                }

                $pdf->Cell(40, 5, $fine->created_at->format('d/m/Y'), 0, 0);
                $pdf->Cell(0, 5, 'R$ ' . number_format($fine->amount ?? 0, 2, ',', '.'), 0, 1);
            }
        }

        // Rodapé
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(107, 114, 128);
        $pdf->Cell(0, 10, 'Gerado em: ' . now()->format('d/m/Y H:i:s') . ' | Usuário: ' . (auth()->user()->name ?? 'Sistema'), 0, 0, 'C');

        // Salvar PDF
        $fileName = 'backup_vehicle_' . $vehicle->plate . '_' . now()->format('YmdHis') . '.pdf';
        $filePath = 'backups/' . $fileName;

        Storage::disk('local')->put($filePath, $pdf->Output('', 'S'));

        // Criar registro do backup
        $metadata = [
            'service_orders_count' => $serviceOrders->count(),
            'fuelings_count' => $fuelings->count(),
            'runs_count' => $runs->count(),
            'fines_count' => $fines->count(),
            'defect_reports_count' => $defectReports->count(),
            'transfers_count' => $transfers->count(),
        ];

        return BackupReport::create([
            'user_id' => auth()->id(),
            'entity_type' => 'Vehicle',
            'entity_id' => $vehicle->id,
            'entity_name' => $vehicle->name . ' - ' . strtoupper($vehicle->plate),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::disk('local')->size($filePath),
            'description' => 'Backup completo do veículo antes da exclusão',
            'metadata' => $metadata,
        ]);
    }

    public function generateCategoryBackup(VehicleCategory $category): BackupReport
    {
        // Buscar veículos relacionados
        $vehicles = $category->vehicles()->with(['prefix', 'status', 'fuelType'])->get();

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        $pdf->SetCreator('Sistema de Frotas');
        $pdf->SetAuthor(auth()->user()->name ?? 'Sistema');
        $pdf->SetTitle('Backup - Categoria ' . $category->name);
        $pdf->SetSubject('Backup de dados da categoria');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 10, 'BACKUP DE CATEGORIA DE VEÍCULO', 0, 1, 'C');
        $pdf->Ln(5);

        // Informações da categoria
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetFillColor(59, 130, 246);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Informações da Categoria', 0, 1, 'L', true);
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 6, 'Nome:', 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 6, $category->name, 0, 1);

        // Veículos relacionados
        if ($vehicles->count() > 0) {
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Veículos nesta Categoria (' . $vehicles->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(31, 41, 55);
            foreach ($vehicles as $vehicle) {
                $pdf->Cell(60, 5, $vehicle->name, 0, 0);
                $pdf->Cell(40, 5, strtoupper($vehicle->plate), 0, 0);
                $pdf->Cell(0, 5, $vehicle->brand, 0, 1);
            }
        }

        // Rodapé
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(107, 114, 128);
        $pdf->Cell(0, 10, 'Gerado em: ' . now()->format('d/m/Y H:i:s') . ' | Usuário: ' . (auth()->user()->name ?? 'Sistema'), 0, 0, 'C');

        // Salvar PDF
        $fileName = 'backup_category_' . str_replace(' ', '_', $category->name) . '_' . now()->format('YmdHis') . '.pdf';
        $filePath = 'backups/' . $fileName;

        Storage::disk('local')->put($filePath, $pdf->Output('', 'S'));

        return BackupReport::create([
            'user_id' => auth()->id(),
            'entity_type' => 'VehicleCategory',
            'entity_id' => $category->id,
            'entity_name' => $category->name,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::disk('local')->size($filePath),
            'description' => 'Backup completo da categoria antes da exclusão',
            'metadata' => ['vehicles_count' => $vehicles->count()],
        ]);
    }

    public function generatePrefixBackup(Prefix $prefix): BackupReport
    {
        // Buscar veículos relacionados
        $vehicles = $prefix->vehicles()->with(['category', 'status', 'fuelType'])->get();

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        $pdf->SetCreator('Sistema de Frotas');
        $pdf->SetAuthor(auth()->user()->name ?? 'Sistema');
        $pdf->SetTitle('Backup - Prefixo ' . $prefix->name);
        $pdf->SetSubject('Backup de dados do prefixo');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 10, 'BACKUP DE PREFIXO', 0, 1, 'C');
        $pdf->Ln(5);

        // Informações do prefixo
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetFillColor(59, 130, 246);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Informações do Prefixo', 0, 1, 'L', true);
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 6, 'Nome:', 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 6, $prefix->name, 0, 1);

        // Veículos relacionados
        if ($vehicles->count() > 0) {
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Veículos com este Prefixo (' . $vehicles->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(31, 41, 55);
            foreach ($vehicles as $vehicle) {
                $pdf->Cell(60, 5, $vehicle->name, 0, 0);
                $pdf->Cell(40, 5, strtoupper($vehicle->plate), 0, 0);
                $pdf->Cell(0, 5, $vehicle->brand, 0, 1);
            }
        }

        // Rodapé
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(107, 114, 128);
        $pdf->Cell(0, 10, 'Gerado em: ' . now()->format('d/m/Y H:i:s') . ' | Usuário: ' . (auth()->user()->name ?? 'Sistema'), 0, 0, 'C');

        // Salvar PDF
        $fileName = 'backup_prefix_' . str_replace(' ', '_', $prefix->name) . '_' . now()->format('YmdHis') . '.pdf';
        $filePath = 'backups/' . $fileName;

        Storage::disk('local')->put($filePath, $pdf->Output('', 'S'));

        return BackupReport::create([
            'user_id' => auth()->id(),
            'entity_type' => 'Prefix',
            'entity_id' => $prefix->id,
            'entity_name' => $prefix->name,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::disk('local')->size($filePath),
            'description' => 'Backup completo do prefixo antes da exclusão',
            'metadata' => ['vehicles_count' => $vehicles->count()],
        ]);
    }
}
