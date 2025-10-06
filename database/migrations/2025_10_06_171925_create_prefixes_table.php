<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prefixes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // Ex: "V-001", "S-102"
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('prefixes'); }
};
