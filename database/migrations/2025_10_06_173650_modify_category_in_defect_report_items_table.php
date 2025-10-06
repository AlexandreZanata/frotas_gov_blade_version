<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('defect_report_items', function (Blueprint $table) {
            // Adiciona a nova coluna de chave estrangeira
            $table->foreignUuid('category_id')->nullable()->after('id')->constrained('defect_categories');

            // Remove a coluna de texto antiga
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('defect_report_items', function (Blueprint $table) {
            // Processo reverso para o rollback
            $table->string('category')->default('Geral')->after('name');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
