<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Corrigido para o plural correto do inglês 'managers'
        Schema::create('secretariat_sector_managers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('secretariat_id')->constrained('secretariats')->cascadeOnDelete();
            $table->foreignUuid('manager_status_id')->constrained('manager_statuses');
            
            $table->unique(['user_id', 'secretariat_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secretariat_sector_managers');
    }
};
