<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_price_origins', function (Blueprint $table) {
            // Adiciona a nova coluna de chave estrangeira (nullable por enquanto)
            $table->foreignUuid('acquisition_type_id')->nullable()->after('acquisition_date')->constrained('acquisition_types');

            // Remove a coluna de texto antiga
            $table->dropColumn('acquisition_type');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_price_origins', function (Blueprint $table) {
            // Processo reverso para o rollback
            $table->string('acquisition_type')->nullable()->after('acquisition_date');
            $table->dropForeign(['acquisition_type_id']);
            $table->dropColumn('acquisition_type_id');
        });
    }
};
