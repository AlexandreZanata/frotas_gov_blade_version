<?php

namespace Database\Seeders;

use App\Models\PdfTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PdfTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PdfTemplate::create([
            'name' => 'Relatório Padrão A4 Retrato',
            'header_text' => "Prefeitura Municipal de [SUA CIDADE]\nRelatório Oficial de Frota",
            'footer_text' => 'Página {PAGENO}/{nb}',
            'footer_text_align' => 'R', // Alinhado à direita
            'table_columns' => json_encode([
                ['header' => 'ID', 'data_key' => 'id', 'width' => 20],
                ['header' => 'Nome', 'data_key' => 'name', 'width' => 80],
                ['header' => 'Placa', 'data_key' => 'plate', 'width' => 40],
                ['header' => 'Status', 'data_key' => 'status', 'width' => 40],
            ]),
            'margin_top' => 25, // Margem maior para cabecalho
            'margin_bottom' => 25, // Margem maior para rodapé
        ]);
    }
}
