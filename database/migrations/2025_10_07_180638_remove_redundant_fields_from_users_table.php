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
        Schema::table('users', function (Blueprint $table) {
            // Remover campos redundantes (manter os mais novos: cnh, cnh_expiration_date)
            $table->dropColumn(['cnh_number', 'cnh_expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recriar campos caso seja necessário fazer rollback
            $table->string('cnh_number')->nullable()->after('cpf');
            $table->date('cnh_expiry_date')->nullable()->after('cnh_number');
        });
    }
};
