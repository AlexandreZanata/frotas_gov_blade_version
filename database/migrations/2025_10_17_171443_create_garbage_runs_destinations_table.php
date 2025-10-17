<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
// Em 2025_10_17_171443_create_garbage_runs_destinations_table.php

    public function up(): void
    {
        Schema::create('garbage_run_destinations', function (Blueprint $table) { // [!code --]
            $table->uuid('id')->primary();
            $table->foreignUuid('garbage_run_id')->constrained('garbage_runs')->cascadeOnDelete();
            $table->enum('type', ['neighborhood', 'comment']);
            $table->foreignUuid('garbage_neighborhood_id')->nullable()->constrained('garbage_neighborhoods');
            $table->text('comment')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garbage_run_destinations'); // [!code --]
    }
};
