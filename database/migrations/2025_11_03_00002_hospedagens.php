<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hospedagens', function (Blueprint $table) {
            $table->id();
            $table->integer('cidade');
            $table->string('nome', 150);
            $table->float('menor_valor', 11,2)->nullable();
            $table->float('maior_valor', 11,2)->nullable();
            $table->float('valor_cafe', 11,2)->nullable();
            $table->float('desconto_parceiro', 11,2)->nullable();
            $table->boolean('parceiro')->nullable();
            $table->string('email', 80)->nullable();
            $table->string('telefone', 11)->nullable();
            $table->string('telefone2', 11)->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('hospedagens');
    }
};
