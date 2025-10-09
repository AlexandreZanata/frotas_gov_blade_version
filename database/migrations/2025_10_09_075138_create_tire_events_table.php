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
        Schema::create('tire_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tire_id')->constrained('tires')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->comment('Usuário que realizou a ação');
            $table->foreignUuid('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null')->comment('Veículo envolvido no evento');
            $table->enum('event_type', ['Cadastro', 'Instalação', 'Rodízio', 'Troca', 'Manutenção', 'Recapagem', 'Descarte']);
            $table->text('description');
            $table->integer('km_at_event')->nullable()->comment('KM do veículo no momento do evento');
            $table->dateTime('event_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tire_events');
    }
};
