<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fuelings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('vehicle_id')->constrained('vehicles');
            $table->foreignUuid('fuel_type_id')->constrained('fuel_types');
            $table->foreignUuid('gas_station_id')->constrained('gas_stations');

            $table->timestamp('fueled_at'); // Combina data e hora
            $table->unsignedInteger('km'); // KM no momento do abastecimento
            $table->decimal('liters', 10, 3);
            $table->decimal('value_per_liter', 10, 2);
            $table->string('invoice_path')->nullable(); // Caminho para a nota fiscal

            $table->string('public_code')->unique(); // Código público para consulta externa
            $table->text('signature_path'); // Caminho para a imagem da assinatura

            $table->json('viewed_by')->nullable(); // Armazena dados de visualização

            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('fuelings'); }
};
