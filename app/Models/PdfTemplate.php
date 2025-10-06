<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'header_image',
        'footer_image',
        'header_scope',
        'header_image_align',
        'header_image_width',
        'header_image_height',
        'header_text',
        'header_text_align',
        'header_line_height',
        'footer_scope',
        'footer_image_align',
        'footer_image_width',
        'footer_image_height',
        'footer_text',
        'footer_text_align',
        'footer_line_height',
        'body_text',
        'table_style',
        'table_header_bg',
        'table_header_text',
        'table_row_height',
        'show_table_lines',
        'use_zebra_stripes',
        'table_columns',
        'cell_text_align_mode',
        'cell_transform',
        'cell_word_wrap',
        'after_table_text',
        'real_time_preview',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'font_family',
        'header_font_family',
        'footer_font_family',
        'font_size_title',
        'header_font_size',
        'footer_font_size',
        'font_size_text',
        'font_size_table',
        'font_style_title',
        'header_font_style',
        'header_image_vertical_position',
        'footer_font_style',
        'footer_image_vertical_position',
        'body_line_height',
        'paragraph_spacing',
        'heading_spacing',
        'font_style_text',
        'font_family_body',
    ];

    protected function casts(): array
    {
        return [
            'table_columns' => 'array', // Converte a coluna JSON para array automaticamente
        ];
    }
}
