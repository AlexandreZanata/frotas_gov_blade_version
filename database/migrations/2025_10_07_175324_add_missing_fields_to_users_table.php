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
            // Adicionar campos que faltam na tabela users
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('cnh', 20)->nullable()->after('phone');
            $table->date('cnh_expiration_date')->nullable()->after('cnh');
            $table->string('cnh_category', 5)->nullable()->after('cnh_expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'cnh', 'cnh_expiration_date', 'cnh_category']);
        });
    }
};
