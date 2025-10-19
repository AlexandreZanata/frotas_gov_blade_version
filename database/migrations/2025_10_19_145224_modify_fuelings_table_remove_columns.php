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
        Schema::table('fuelings', function (Blueprint $table) {

            // 1. Remover a chave estrangeira e a coluna 'signature_id'
            // confirma que o nome da constraint é 'fuelings_signature_id_foreign'
            if (Schema::hasColumn('fuelings', 'signature_id')) {
                // Primeiro remove a constraint usando o nome exato
                $table->dropForeign('fuelings_signature_id_foreign');
                // Depois remove a coluna
                $table->dropColumn('signature_id');
            }

            // 2. Remover a coluna 'viewed_by'
            if (Schema::hasColumn('fuelings', 'viewed_by')) {
                $table->dropColumn('viewed_by');
            }

            // 3. Remover a coluna signature_path (baseado na estrutura antiga de fuelings)
            if (Schema::hasColumn('fuelings', 'signature_path')) {
                $table->dropColumn('signature_path');
            }

            // 4. Adicionar a coluna `value` (presente no Observer mas faltante no dump)
            if (!Schema::hasColumn('fuelings', 'value')) {
                $table->decimal('value', 10, 2)->default(0.00)->after('liters')->comment('Valor total do abastecimento');
            }

            // 5. Adicionar run_id se não existir (baseado no DUMP de fuelings(7).sql)
            if (!Schema::hasColumn('fuelings', 'run_id')) {
                $table->foreignUuid('run_id')->nullable()->after('vehicle_id')->constrained('runs')->nullOnDelete();
            }

            // 6. Adicionar gas_station_name se não existir (baseado no DUMP de fuelings(7).sql)
            if (!Schema::hasColumn('fuelings', 'gas_station_name')) {
                $table->string('gas_station_name')->nullable()->after('gas_station_id');
            }

            // 7. Modificar public_code para ser nullable (baseado no DUMP de fuelings(7).sql)
            if (Schema::hasColumn('fuelings', 'public_code')) {
                $table->string('public_code')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuelings', function (Blueprint $table) {
            // Recriar as colunas na ordem inversa da remoção (se necessário rollback)
            if (!Schema::hasColumn('fuelings', 'signature_path')) {
                $table->text('signature_path')->nullable();
            }

            if (!Schema::hasColumn('fuelings', 'viewed_by')) {
                $table->json('viewed_by')->nullable();
            }

            if (!Schema::hasColumn('fuelings', 'signature_id')) {
                $table->uuid('signature_id')->nullable();
                // Recria a chave estrangeira (baseado no fuelings(7).sql)
                $table->foreign('signature_id', 'fuelings_signature_id_foreign')
                    ->references('id')->on('fuelings_signatures');
            }

            // Remover colunas adicionadas no 'up'
            if (Schema::hasColumn('fuelings', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('fuelings', 'run_id')) {
                $table->dropForeign(['run_id']);
                $table->dropColumn('run_id');
            }
            if (Schema::hasColumn('fuelings', 'gas_station_name')) {
                $table->dropColumn('gas_station_name');
            }


        });
    }
};
