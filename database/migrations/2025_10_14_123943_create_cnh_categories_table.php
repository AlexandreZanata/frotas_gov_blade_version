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
        Schema::create('cnh_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 5)->unique(); // A, B, C, D, E, AB, etc.
            $table->string('name'); // Categoria A, Categoria B, etc.
            $table->text('description')->nullable(); // Descrição da categoria
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index para melhor performance
            $table->index(['code', 'is_active']);
        });

        // Inserir categorias padrão
        $this->seedDefaultCategories();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cnh_categories');
    }

    /**
     * Inserir categorias padrão
     */
    private function seedDefaultCategories(): void
    {
        $categories = [
            ['code' => 'A', 'name' => 'Categoria A', 'description' => 'Veículos motorizados de 2 ou 3 rodas'],
            ['code' => 'B', 'name' => 'Categoria B', 'description' => 'Veículos motorizados não abrangidos pela categoria A, cujo peso bruto total não exceda a 3.500 kg'],
            ['code' => 'C', 'name' => 'Categoria C', 'description' => 'Veículos motorizados utilizados no transporte de carga, cujo peso bruto total exceda a 3.500 kg'],
            ['code' => 'D', 'name' => 'Categoria D', 'description' => 'Veículos motorizados utilizados no transporte de passageiros, cuja lotação exceda a 8 lugares'],
            ['code' => 'E', 'name' => 'Categoria E', 'description' => 'Combinação de veículos em que o veículo trator se enquadre nas categorias B, C ou D'],
            ['code' => 'AB', 'name' => 'Categoria AB', 'description' => 'Categorias A e B'],
            ['code' => 'ACC', 'name' => 'Categoria ACC', 'description' => 'Autorização para Conduzir Ciclomotor'],
        ];

        foreach ($categories as $category) {
            \DB::table('cnh_categories')->insert([
                'id' => \Illuminate\Support\Str::uuid(),
                'code' => $category['code'],
                'name' => $category['name'],
                'description' => $category['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
