<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Criar a tabela de destinos se não existir
        if (!Schema::hasTable('run_destinations')) {
            Schema::create('run_destinations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('run_id');
                $table->string('destination');
                $table->integer('order')->default(0);
                $table->timestamps();

                $table->foreign('run_id')
                    ->references('id')
                    ->on('runs')
                    ->onDelete('cascade');

                $table->index(['run_id', 'order']);
            });
        }

        // 2. Remover a coluna destination da tabela runs se existir
        if (Schema::hasColumn('runs', 'destination')) {
            Schema::table('runs', function (Blueprint $table) {
                $table->dropColumn('destination');
            });
        }
    }

    public function down(): void
    {
        // 1. Recriar a coluna destination na tabela runs
        if (!Schema::hasColumn('runs', 'destination')) {
            Schema::table('runs', function (Blueprint $table) {
                $table->string('destination')->nullable()->after('user_id');
            });
        }

        // 2. Migrar dados de volta (opcional - apenas o primeiro destino)
        if (Schema::hasTable('run_destinations')) {
            $primaryDestinations = DB::table('run_destinations')
                ->where('order', 0)
                ->get();

            foreach ($primaryDestinations as $destination) {
                DB::table('runs')
                    ->where('id', $destination->run_id)
                    ->update(['destination' => $destination->destination]);
            }
        }

        // 3. Remover a tabela de destinos
        Schema::dropIfExists('run_destinations');
    }
};
