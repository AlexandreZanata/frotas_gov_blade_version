<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique()->comment('Nome interno do status (ex: active, inactive)');
            $table->string('description')->comment('Descrição amigável para exibição (ex: Ativo, Inativo)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_statuses');
    }
};
