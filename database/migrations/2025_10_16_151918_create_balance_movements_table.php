<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users'); // Usuário responsável pela movimentação
            $table->uuidMorphs('movable'); // Polimorfismo: `movable_id` e `movable_type`
            $table->enum('type', ['debit', 'credit']); // Débito (saída), Crédito (entrada/estorno)
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->timestamp('moved_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_movements');
    }
};
