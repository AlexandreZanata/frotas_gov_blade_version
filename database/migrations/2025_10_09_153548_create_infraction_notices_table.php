<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('infraction_notices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('notice_number')->unique(); // Número do auto de infração
            $table->string('security_code')->unique(); // Código de segurança para verificação
            $table->text('description')->nullable();
            $table->timestamp('issued_at'); // Data de emissão
            $table->string('issuing_authority')->nullable(); // Autoridade emissora
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infraction_notices');
    }
};
