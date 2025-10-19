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
        Schema::table('fuelings', function (Blueprint $table) {
            // Adicionar o campo gas_station_name
            $table->string('gas_station_name')->nullable()->after('gas_station_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuelings', function (Blueprint $table) {
            $table->dropColumn('gas_station_name');
        });
    }
};
