<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_commitments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('secretariat_id')->constrained('secretariats');
            $table->foreignUuid('supplier_id')->constrained('balance_gas_station_suppliers');
            $table->string('number')->unique(); // Número do empenho
            $table->year('year');
            $table->date('date');
            $table->decimal('total_value', 15, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'partially_paid', 'paid', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_commitments');
    }
};
