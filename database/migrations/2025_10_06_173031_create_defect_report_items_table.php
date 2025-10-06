<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('defect_report_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Ex: "Freio com ruído", "Luz de injeção acesa"
            $table->string('category')->default('Geral'); // Ex: Motor, Freios, Elétrica
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('defect_report_items'); }
};
