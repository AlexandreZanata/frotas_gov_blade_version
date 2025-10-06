<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defect_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // Ex: Motor, Freios, Elétrica
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defect_categories');
    }
};
