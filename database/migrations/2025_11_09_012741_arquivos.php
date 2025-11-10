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
        Schema::create('arquivos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('tipo_relacionamento_id');
        $table->unsignedBigInteger('relacionamento_id');
        $table->string('nome_original');
        $table->string('caminho');
        $table->string('tipo_mime')->nullable();
        $table->integer('tamanho')->nullable();
        $table->string('extensao', 10)->nullable();
        $table->timestamp('criado_em')->useCurrent();
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
        Schema::dropIfExists('arquivos');
    }
};
