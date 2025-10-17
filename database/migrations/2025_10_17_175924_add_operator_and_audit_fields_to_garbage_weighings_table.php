<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garbage_weighings', function (Blueprint $table) {
            $table->string('weighing_code')->unique()->after('id');

            $table->foreignUuid('garbage_type_id')->nullable()->after('id')->constrained('garbage_types');

            $table->foreignUuid('weighbridge_operator_id')->nullable()->after('user_id')->constrained('garbage_weighbridge_operators');

            $table->foreignUuid('garbage_vehicle_id')->after('weighbridge_operator_id')->constrained('garbage_vehicles');

            $table->renameColumn('user_id', 'requester_id');
        });
    }

    public function down(): void
    {
        Schema::table('garbage_weighings', function (Blueprint $table) {
            $table->dropForeign(['garbage_type_id']);
            $table->dropForeign(['weighbridge_operator_id']);
            $table->dropForeign(['garbage_vehicle_id']);
            $table->dropColumn(['weighing_code', 'garbage_type_id', 'weighbridge_operator_id', 'garbage_vehicle_id']);
            $table->renameColumn('requester_id', 'user_id');
        });
    }
};
