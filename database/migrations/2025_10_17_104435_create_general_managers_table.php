<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_managers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();

            $table->foreignUuid('manager_status_id')->constrained('manager_statuses');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_managers');
    }
};
