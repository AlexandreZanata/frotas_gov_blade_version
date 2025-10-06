<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');

            // --- Header ---
            $table->string('header_image')->nullable();
            $table->string('header_scope')->default('all'); // all, first, last
            $table->string('header_image_align')->default('C'); // L, C, R
            $table->integer('header_image_width')->nullable();
            $table->integer('header_image_height')->nullable();
            $table->text('header_text')->nullable();
            $table->string('header_text_align')->default('C'); // L, C, R
            $table->float('header_line_height')->default(1.2);
            $table->string('header_image_vertical_position')->default('inline-left');

            // --- Footer ---
            $table->string('footer_image')->nullable();
            $table->string('footer_scope')->default('all'); // all, first, last
            $table->string('footer_image_align')->default('C'); // L, C, R
            $table->integer('footer_image_width')->nullable();
            $table->integer('footer_image_height')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('footer_text_align')->default('C'); // L, C, R
            $table->float('footer_line_height')->default(1.2);
            $table->string('footer_image_vertical_position')->default('inline-left');

            // --- Body & Text ---
            $table->text('body_text')->nullable();
            $table->text('after_table_text')->nullable();
            $table->float('body_line_height')->default(1.5);
            $table->float('paragraph_spacing')->default(5);
            $table->float('heading_spacing')->default(8);

            // --- Table Style ---
            $table->string('table_style')->default('grid');
            $table->string('table_header_bg')->default('#f3f4f6');
            $table->string('table_header_text')->default('#374151');
            $table->integer('table_row_height')->default(10);
            $table->boolean('show_table_lines')->default(true);
            $table->boolean('use_zebra_stripes')->default(false);
            $table->json('table_columns')->nullable(); // JSON é ideal para armazenar a estrutura das colunas

            // --- Cell Style ---
            $table->string('cell_text_align_mode', 10)->default('auto');
            $table->string('cell_transform', 20)->default('none');
            $table->boolean('cell_word_wrap')->default(true);

            // --- Document Settings ---
            $table->boolean('real_time_preview')->default(true);
            $table->integer('margin_top')->default(10);
            $table->integer('margin_bottom')->default(10);
            $table->integer('margin_left')->default(10);
            $table->integer('margin_right')->default(10);

            // --- Font Settings ---
            $table->string('font_family')->default('helvetica');
            $table->string('font_family_body')->default('helvetica');
            $table->string('header_font_family')->default('helvetica');
            $table->string('footer_font_family')->default('helvetica');
            $table->integer('font_size_title')->default(16);
            $table->integer('header_font_size')->default(12);
            $table->integer('footer_font_size')->default(10);
            $table->integer('font_size_text')->default(12);
            $table->integer('font_size_table')->default(10);
            $table->string('font_style_title')->nullable(); // B, I, U
            $table->string('header_font_style')->nullable();
            $table->string('footer_font_style')->nullable();
            $table->string('font_style_text')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_templates');
    }
};
