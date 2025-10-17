<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garbage_weighings', function (Blueprint $table) {
            $table->renameColumn('weight_kg', 'gross_weight_kg');

            $table->decimal('tare_weight_kg', 10, 2)->after('gross_weight_kg');
            $table->decimal('net_weight_kg', 10, 2)->after('tare_weight_kg');
        });
    }

    public function down(): void
    {
        Schema::table('garbage_weighings', function (Blueprint $table) {
            $table->renameColumn('gross_weight_kg', 'weight_kg');
            $table->dropColumn(['tare_weight_kg', 'net_weight_kg']);
        });
    }
};
