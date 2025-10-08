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
        Schema::table('runs', function (Blueprint $table) {
            // Tornar campos opcionais pois serão preenchidos após o checklist
            $table->unsignedBigInteger('start_km')->nullable()->change();
            $table->timestamp('started_at')->nullable()->change();
            $table->string('destination')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('runs', function (Blueprint $table) {
            // Reverter para obrigatório
            $table->unsignedBigInteger('start_km')->nullable(false)->change();
            $table->timestamp('started_at')->nullable(false)->change();
            $table->string('destination')->nullable(false)->change();
        });
    }
};
