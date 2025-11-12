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
        Schema::create('prestadores_regioes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prestador_id');
            $table->string('estado');
            $table->string('cidade');
            $table->string('bairro')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('cep_inicio')->nullable();
            $table->string('cep_fim')->nullable();
            $table->decimal('raio_cobertura_km', 8, 2)->nullable()->default(0);
            $table->decimal('valor_deslocamento', 10, 2)->nullable()->default(0);
            $table->text('observacoes')->nullable();
            $table->tinyInteger('ativo')->default(1);
            $table->timestamps();
            
            $table->foreign('prestador_id')->references('id')->on('prestadores')->onDelete('cascade');
            
            $table->index('prestador_id');
            $table->index(['estado', 'cidade']);
            $table->index('cep_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestadores_regioes');
    }
};
