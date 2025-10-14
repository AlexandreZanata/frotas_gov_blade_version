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
            // Adicionar a coluna cnh_category_id
            $table->uuid('cnh_category_id')
                ->nullable()
                ->after('cnh_category'); // Colocar após a coluna antiga

            // Foreign key constraint
            $table->foreign('cnh_category_id')
                ->references('id')
                ->on('cnh_categories')
                ->onDelete('set null');

            // Manter a coluna antiga por um tempo para migração
            // Vamos remover depois que migrarmos os dados
        });

        // Migrar dados da coluna antiga para a nova
        $this->migrateExistingCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cnh_category_id']);
            $table->dropColumn('cnh_category_id');
        });
    }

    /**
     * Migrar categorias existentes da coluna antiga para a nova
     */
    private function migrateExistingCategories(): void
    {
        $categoryMap = [
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
            'E' => 'E',
            'AB' => 'AB',
            'ACC' => 'ACC',
        ];

        foreach ($categoryMap as $oldCode => $newCode) {
            $category = \DB::table('cnh_categories')->where('code', $newCode)->first();
            if ($category) {
                \DB::table('users')
                    ->where('cnh_category', $oldCode)
                    ->update(['cnh_category_id' => $category->id]);
            }
        }
    }
};
