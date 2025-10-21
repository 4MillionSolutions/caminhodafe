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
        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();
            $table->string('numero',10);
            $table->string('complemento',100);
            $table->string('cep',10);
            $table->string('endereco',180);
            $table->string('bairro',100);
            $table->string('cidade',100);
            $table->string('estado',2);
            $table->string('tipo',50);
            $table->string('responsavel',100);
            $table->string('telefone',11);
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
