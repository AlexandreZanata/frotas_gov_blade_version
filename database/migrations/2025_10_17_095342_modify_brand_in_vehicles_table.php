<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignUuid('brand_id')->nullable()->after('name')->constrained('vehicle_brands');

            $table->dropColumn('brand');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('brand')->after('name');
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
};
