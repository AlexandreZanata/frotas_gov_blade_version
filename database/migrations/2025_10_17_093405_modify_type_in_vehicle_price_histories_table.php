<?php

// database/migrations/xxxx_xx_xx_xxxxxx_modify_type_in_vehicle_price_histories_table.php
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
        Schema::table('vehicle_price_histories', function (Blueprint $table) {
            $table->foreignUuid('acquisition_type_id')->nullable()->after('user_id')->constrained('acquisition_types');

            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_price_histories', function (Blueprint $table) {
            $table->enum('type', ['credit', 'debit'])->after('user_id');
            $table->dropForeign(['acquisition_type_id']);
            $table->dropColumn('acquisition_type_id');
        });
    }
};
