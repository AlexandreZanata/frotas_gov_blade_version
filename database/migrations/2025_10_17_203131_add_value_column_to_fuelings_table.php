<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuelings', function (Blueprint $table) {
            $table->decimal('value', 10, 2)->after('liters'); // Valor total do abastecimento

            $table->decimal('value_per_liter', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('fuelings', function (Blueprint $table) {
            $table->dropColumn('value');
            $table->decimal('value_per_liter', 10, 2)->nullable(false)->change(); // Reverte para não anulável
        });
    }
};
