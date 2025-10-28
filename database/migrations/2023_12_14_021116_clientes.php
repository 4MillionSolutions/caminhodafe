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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome_empresa',100);
            $table->string('nome',100)->nullable();
            $table->string('tipo_pessoa',1);
            $table->string('documento',20)->nullable();
            $table->string('endereco', 200)->nullable();
            $table->string('complemento', 200)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('bairro', 150)->nullable();
            $table->string('cidade', 150)->nullable();
            $table->string('estado', 150)->nullable();
            $table->string('telefone', 11)->nullable();
            $table->string('email', 150)->nullable();
            $table->longText('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
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
        //
    }
};
