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
        'header_image', 'header_scope', 'header_image_align', 'header_image_width', 'header_image_height',
        'header_text', 'header_text_align', 'header_line_height', 'header_image_vertical_position',
        'footer_image', 'footer_scope', 'footer_image_align', 'footer_image_width', 'footer_image_height',
        'footer_text', 'footer_text_align', 'footer_line_height', 'footer_image_vertical_position',
        'body_text', 'after_table_text', 'body_line_height', 'paragraph_spacing', 'heading_spacing',
        'table_style', 'table_header_bg', 'table_header_text', 'table_row_height',
        'show_table_lines', 'use_zebra_stripes', 'table_columns',
        'cell_text_align_mode', 'cell_transform', 'cell_word_wrap', 'real_time_preview',
        'margin_top', 'margin_bottom', 'margin_left', 'margin_right',
        'font_family', 'font_family_body', 'header_font_family', 'footer_font_family',
        'font_size_title', 'header_font_size', 'footer_font_size', 'font_size_text', 'font_size_table',
        'font_style_title', 'header_font_style', 'footer_font_style', 'font_style_text',
    ];

    protected $casts = [
        'table_columns' => 'array',
        'show_table_lines' => 'boolean',
        'use_zebra_stripes' => 'boolean',
        'real_time_preview' => 'boolean',
        'cell_word_wrap' => 'boolean',
        'header_line_height' => 'float',
        'footer_line_height' => 'float',
        'body_line_height' => 'float',
        'paragraph_spacing' => 'float',
        'heading_spacing' => 'float',
    ];
}
