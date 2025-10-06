<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('defect_report_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('defect_report_id')->constrained('defect_reports')->cascadeOnDelete();
            $table->foreignUuid('defect_report_item_id')->constrained('defect_report_items')->cascadeOnDelete();
            $table->enum('severity', ['low', 'medium', 'high']); // Gravidade do defeito
            $table->text('notes')->nullable(); // Detalhes específicos sobre o item
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('defect_report_answers'); }
};
