<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Corresponde a 'veiculo'
            $table->string('brand'); // Corresponde a 'marca'
            $table->string('model_year'); // Corresponde a 'ano_modelo'
            $table->string('plate')->unique(); // Corresponde a 'placa'
            $table->string('chassis')->unique()->nullable(); // Corresponde a 'chassi'
            $table->string('renavam')->unique()->nullable(); // Corresponde a 'renavam'
            $table->string('registration')->nullable(); // Corresponde a 'matricula'
            $table->integer('fuel_tank_capacity'); // Corresponde a 'tanque'

            // Chaves Estrangeiras
            $table->foreignUuid('fuel_type_id')->constrained('fuel_types');
            $table->foreignUuid('category_id')->constrained('vehicle_categories');
            $table->foreignUuid('status_id')->constrained('vehicle_statuses');
            $table->foreignUuid('secretariat_id')->constrained('secretariats');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
