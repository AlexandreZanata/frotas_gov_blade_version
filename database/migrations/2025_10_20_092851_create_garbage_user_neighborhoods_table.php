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
        // Tabela pivot para associar GarbageUser aos Bairros permitidos
        Schema::create('garbage_user_neighborhoods', function (Blueprint $table) {
            // Chave primária composta ou UUID individual? Usaremos UUID por padrão.
            $table->uuid('id')->primary();

            // Chave estrangeira para a tabela garbage_users
            // Usando cascadeOnDelete: se o registro do garbage_user for deletado,
            // as associações de bairro também são removidas.
            $table->foreignUuid('garbage_user_id')
                ->constrained('garbage_users') // Nome da tabela garbage_users
                ->cascadeOnDelete();

            // Chave estrangeira para a tabela garbage_neighborhoods
            // Usando cascadeOnDelete: se o bairro for deletado,
            // as associações com usuários também são removidas.
            $table->foreignUuid('garbage_neighborhood_id')
                ->constrained('garbage_neighborhoods') // Nome da tabela garbage_neighborhoods
                ->cascadeOnDelete();

            $table->timestamps(); // Opcional: Se precisar saber quando a associação foi criada/atualizada

            // Garante que a combinação de usuário e bairro seja única
            $table->unique(['garbage_user_id', 'garbage_neighborhood_id'], 'user_neighborhood_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garbage_user_neighborhoods');
    }
};
