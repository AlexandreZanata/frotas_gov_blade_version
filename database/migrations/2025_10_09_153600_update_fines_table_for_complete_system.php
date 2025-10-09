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
        Schema::table('fines', function (Blueprint $table) {
            // Adicionar referência ao auto de infração
            $table->foreignUuid('infraction_notice_id')->nullable()->after('id')->constrained('infraction_notices')->nullOnDelete();

            // Adicionar campo para notificação enviada
            $table->boolean('notification_sent')->default(false)->after('status');
            $table->timestamp('notification_sent_at')->nullable()->after('notification_sent');

            // Adicionar campo para ciência do condutor
            $table->boolean('acknowledged_by_driver')->default(false)->after('notification_sent_at');
            $table->timestamp('acknowledged_at')->nullable()->after('acknowledged_by_driver');

            // Campo para primeira visualização por admin
            $table->timestamp('first_viewed_at')->nullable()->after('acknowledged_at');
            $table->foreignUuid('first_viewed_by')->nullable()->after('first_viewed_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            $table->dropForeign(['infraction_notice_id']);
            $table->dropForeign(['first_viewed_by']);
            $table->dropColumn([
                'infraction_notice_id',
                'notification_sent',
                'notification_sent_at',
                'acknowledged_by_driver',
                'acknowledged_at',
                'first_viewed_at',
                'first_viewed_by'
            ]);
        });
    }
};
