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
        // Service Orders
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        // Fuelings
        Schema::table('fuelings', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        // Runs
        Schema::table('runs', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        // Fines
        Schema::table('fines', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        // Defect Reports
        Schema::table('defect_reports', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        // Vehicle Transfers
        Schema::table('vehicle_transfers', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter as alterações (opcional - pode deixar como está para segurança)
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });

        Schema::table('fuelings', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });

        Schema::table('runs', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });

        Schema::table('defect_reports', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });

        Schema::table('vehicle_transfers', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });
    }
};
