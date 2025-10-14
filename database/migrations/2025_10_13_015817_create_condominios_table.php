<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condominios', function (Blueprint $table) {
            $table->id();
            
            // Campos baseados no Excel - Tabela Condomínios
            $table->string('nome')->nullable();
            $table->decimal('area_terreno', 10, 2)->nullable();
            $table->decimal('area_privativa', 10, 2)->nullable();
            $table->foreignId('laudo_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('condominios');
    }
};
