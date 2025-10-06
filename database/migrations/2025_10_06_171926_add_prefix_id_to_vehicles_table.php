<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignUuid('prefix_id')->nullable()->after('id')->constrained('prefixes');
        });
    }
    public function down(): void {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['prefix_id']);
            $table->dropColumn('prefix_id');
        });
    }
};
