<?php

namespace App\Services;

use App\Models\auditlog\BackupReport;
use App\Models\PdfTemplate;
use App\Models\Vehicle\Prefix;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleCategory;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class BackupPdfService
{
    /**
     * Aplicar template ao PDF se disponível
     */
    protected function applyTemplate(TCPDF $pdf, ?PdfTemplate $template = null): void
    {
        if (!$template) {
            return;
        }

        // Aplicar header personalizado
        if ($template->header_image && Storage::disk('public')->exists($template->header_image)) {
            $imagePath = Storage::disk('public')->path($template->header_image);

            // Configurar posição e tamanho da imagem
            $width = $template->header_image_width ?? 50;
            $height = $template->header_image_height ?? 0; // 0 = auto

            // Determinar alinhamento
            $x = match($template->header_image_align ?? 'C') {
                'L' => $template->margin_left ?? 15,
                'R' => 210 - ($template->margin_right ?? 15) - $width,
                default => (210 - $width) / 2, // Centro
            };

            $y = $template->margin_top ?? 10;

            // Inserir imagem
            $pdf->Image($imagePath, $x, $y, $width, $height, '', '', '', false, 300, '', false, false, 0);
        }

        // Aplicar margens do template
        $pdf->SetMargins(
            $template->margin_left ?? 15,
            $template->margin_top ?? 15,
            $template->margin_right ?? 15
        );
        $pdf->SetAutoPageBreak(true, $template->margin_bottom ?? 15);
    }

    /**
     * Adicionar footer com template
     */
    protected function addFooterWithTemplate(TCPDF $pdf, ?PdfTemplate $template = null): void
    {
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', $template?->footer_font_size ?? 8);
        $pdf->SetTextColor(107, 114, 128);

        $footerText = 'Gerado em: ' . now()->format('d/m/Y H:i:s') . ' | Usuário: ' . (auth()->user()->name ?? 'Sistema');

        if ($template && $template->footer_text) {
            $footerText = $template->footer_text . ' | ' . $footerText;
        }

        $align = match($template?->footer_text_align ?? 'C') {
            'L' => 'L',
            'R' => 'R',
            default => 'C',
        };

        $pdf->Cell(0, 10, $footerText, 0, 0, $align);

        // Adicionar imagem do footer se existir
        if ($template && $template->footer_image && Storage::disk('public')->exists($template->footer_image)) {
            $imagePath = Storage::disk('public')->path($template->footer_image);
            $width = $template->footer_image_width ?? 40;
            $height = $template->footer_image_height ?? 0;

            $x = match($template->footer_image_align ?? 'C') {
                'L' => $template->margin_left ?? 15,
                'R' => 210 - ($template->margin_right ?? 15) - $width,
                default => (210 - $width) / 2,
            };

            $y = 297 - ($template->margin_bottom ?? 15) - ($height > 0 ? $height : 10);

            $pdf->Image($imagePath, $x, $y, $width, $height, '', '', '', false, 300, '', false, false, 0);
        }
    }

    public function generateVehicleBackup(Vehicle $vehicle, ?PdfTemplate $template = null): BackupReport
    {
        $vehicle->load(['category', 'prefix', 'status', 'fuelType']);

        // Buscar dados relacionados com as relações corretas
        $serviceOrders = $vehicle->serviceOrders()->with('items', 'mechanic', 'quoteApprover')->get();
        $fuelings = $vehicle->fuelings()->with('gasStation')->get();
        $runs = $vehicle->runs()->with('signatures')->get();
        $defectReports = $vehicle->defectReports()->with('answers')->get();
        $fines = $vehicle->fines()->get();
        $transfers = $vehicle->transfers()->with('originSecretariat', 'destinationSecretariat')->get(); // Corrigido: nomes corretos das relações

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        // Configurações do PDF
        $pdf->SetCreator('Sistema de Frotas');
        $pdf->SetAuthor(auth()->user()->name ?? 'Sistema');
        $pdf->SetTitle('Backup - Veículo ' . $vehicle->plate);
        $pdf->SetSubject('Backup de dados do veículo');

        // Remove header/footer padrão
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Aplicar template se fornecido
        $this->applyTemplate($pdf, $template);

        // Se não houver template, usar margens padrão
        if (!$template) {
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);
        }

        $pdf->AddPage();

        // Título
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', $template?->font_size_title ?? 18);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 10, 'BACKUP DE DADOS DO VEÍCULO', 0, 1, 'C');
        $pdf->Ln(5);

        // Informações do veículo
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
        $pdf->SetFillColor(59, 130, 246);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Informações do Veículo', 0, 1, 'L', true);
        $pdf->Ln(2);

        $pdf->SetFont($template?->font_family ?? 'helvetica', '', $template?->font_size_text ?? 10);
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
            $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 9);
            $pdf->Cell(50, 6, $label . ':', 0, 0);
            $pdf->SetFont($template?->font_family ?? 'helvetica', '', 9);
            $pdf->Cell(0, 6, $value, 0, 1);
        }

        // Ordens de Serviço
        if ($serviceOrders->count() > 0) {
            $pdf->Ln(5);
            $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
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

                $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 9);
                $pdf->Cell(0, 6, 'OS #' . substr($so->id, 0, 8) . ' - ' . $so->created_at->format('d/m/Y'), 0, 1);
                $pdf->SetFont($template?->font_family ?? 'helvetica', '', 8);
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
            $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Abastecimentos (' . $fuelings->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont($template?->font_family ?? 'helvetica', '', 8);
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
            $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Viagens (' . $runs->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont($template?->font_family ?? 'helvetica', '', 8);
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
            $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Multas (' . $fines->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont($template?->font_family ?? 'helvetica', '', 8);
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
        $this->addFooterWithTemplate($pdf, $template);

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
            'template_used' => $template?->name,
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

    public function generateCategoryBackup(VehicleCategory $category, ?PdfTemplate $template = null): BackupReport
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

        $this->applyTemplate($pdf, $template);

        if (!$template) {
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);
        }

        $pdf->AddPage();

        // Título
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', $template?->font_size_title ?? 18);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 10, 'BACKUP DE CATEGORIA DE VEÍCULO', 0, 1, 'C');
        $pdf->Ln(5);

        // Informações da categoria
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
        $pdf->SetFillColor(59, 130, 246);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Informações da Categoria', 0, 1, 'L', true);
        $pdf->Ln(2);

        $pdf->SetFont($template?->font_family ?? 'helvetica', '', $template?->font_size_text ?? 10);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 9);
        $pdf->Cell(30, 6, 'Nome:', 0, 0);
        $pdf->SetFont($template?->font_family ?? 'helvetica', '', 9);
        $pdf->Cell(0, 6, $category->name, 0, 1);

        // Veículos relacionados
        if ($vehicles->count() > 0) {
            $pdf->Ln(5);
            $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Veículos nesta Categoria (' . $vehicles->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont($template?->font_family ?? 'helvetica', '', 8);
            $pdf->SetTextColor(31, 41, 55);
            foreach ($vehicles as $vehicle) {
                $pdf->Cell(60, 5, $vehicle->name, 0, 0);
                $pdf->Cell(40, 5, strtoupper($vehicle->plate), 0, 0);
                $pdf->Cell(0, 5, $vehicle->brand, 0, 1);
            }
        }

        // Rodapé
        $this->addFooterWithTemplate($pdf, $template);

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
            'metadata' => [
                'vehicles_count' => $vehicles->count(),
                'template_used' => $template?->name,
            ],
        ]);
    }

    public function generatePrefixBackup(Prefix $prefix, ?PdfTemplate $template = null): BackupReport
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

        $this->applyTemplate($pdf, $template);

        if (!$template) {
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);
        }

        $pdf->AddPage();

        // Título
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', $template?->font_size_title ?? 18);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->Cell(0, 10, 'BACKUP DE PREFIXO', 0, 1, 'C');
        $pdf->Ln(5);

        // Informações do prefixo
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
        $pdf->SetFillColor(59, 130, 246);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'Informações do Prefixo', 0, 1, 'L', true);
        $pdf->Ln(2);

        $pdf->SetFont($template?->font_family ?? 'helvetica', '', $template?->font_size_text ?? 10);
        $pdf->SetTextColor(31, 41, 55);
        $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 9);
        $pdf->Cell(30, 6, 'Nome:', 0, 0);
        $pdf->SetFont($template?->font_family ?? 'helvetica', '', 9);
        $pdf->Cell(0, 6, $prefix->name, 0, 1);

        // Veículos relacionados
        if ($vehicles->count() > 0) {
            $pdf->Ln(5);
            $pdf->SetFont($template?->font_family ?? 'helvetica', 'B', 14);
            $pdf->SetFillColor(59, 130, 246);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, 'Veículos com este Prefixo (' . $vehicles->count() . ')', 0, 1, 'L', true);
            $pdf->Ln(2);

            $pdf->SetFont($template?->font_family ?? 'helvetica', '', 8);
            $pdf->SetTextColor(31, 41, 55);
            foreach ($vehicles as $vehicle) {
                $pdf->Cell(60, 5, $vehicle->name, 0, 0);
                $pdf->Cell(40, 5, strtoupper($vehicle->plate), 0, 0);
                $pdf->Cell(0, 5, $vehicle->brand, 0, 1);
            }
        }

        // Rodapé
        $this->addFooterWithTemplate($pdf, $template);

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
            'metadata' => [
                'vehicles_count' => $vehicles->count(),
                'template_used' => $template?->name,
            ],
        ]);
    }
}
