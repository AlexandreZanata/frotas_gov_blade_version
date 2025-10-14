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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('photo_cnh_id')
                ->nullable()
                ->after('photo_id');

            // Foreign key constraint
            $table->foreign('photo_cnh_id')
                ->references('id')
                ->on('user_photos_cnh')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['photo_cnh_id']);
            $table->dropColumn('photo_cnh_id');
        });
    }
};
