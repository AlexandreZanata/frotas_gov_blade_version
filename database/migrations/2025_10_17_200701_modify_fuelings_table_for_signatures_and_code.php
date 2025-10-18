<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fuelings', function (Blueprint $table) {

            $table->dropColumn('signature_path');

            $table->foreignUuid('signature_id')->nullable()->after('public_code')->constrained('fuelings_signatures');


            $table->string('public_code')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('fuelings', function (Blueprint $table) {
            // Reverte as alterações
            $table->dropForeign(['signature_id']);
            $table->dropColumn('signature_id');
            $table->text('signature_path')->after('public_code'); // Adiciona a coluna antiga de volta
            $table->string('public_code')->unique()->change(); // Restaura unique se removido
        });
    }
};
