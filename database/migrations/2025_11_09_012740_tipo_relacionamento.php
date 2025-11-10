<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tipo_relacionamento', function (Blueprint $table) {
            $table->id();
            $table->string('descricao', 100)->unique()->comment('Ex: Cliente, Prestador de Serviço, Imóvel');
            $table->text('observacao')->nullable()->comment('Campo opcional para observações adicionais');
            $table->boolean('ativo')->default(true)->comment('Define se o tipo está ativo');
            $table->timestamps();
        });

        // Inserir tipos de relacionamento padrão
        $tiposRelacionamento = [
            ['id' => 1, 'descricao' => 'Cliente', 'observacao' => 'Tipo de relacionamento para clientes'],
            ['id' => 2, 'descricao' => 'Prestador', 'observacao' => 'Tipo de relacionamento para prestadores de serviço'],
            ['id' => 3, 'descricao' => 'Ordem de Serviço', 'observacao' => 'Tipo de relacionamento para ordens de serviço'],
        ];
        foreach ($tiposRelacionamento as $tipo) {
            DB::table('tipo_relacionamento')->insert([
                'descricao' => $tipo['descricao'],
                'observacao' => $tipo['observacao'],
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_relacionamento');
    }
};
