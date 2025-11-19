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
            $table->string('nome', 80)->unique();
            $table->float('menor_valor', 11,2)->nullable();
            $table->float('maior_valor', 11,2)->nullable();
            $table->float('valor_cafe', 11,2)->nullable();
            $table->float('desconto_parceiro', 11,2)->nullable();
            $table->boolean('parceiro')->default(true);
            $table->string('email', 80)->default(true);
            $table->string('telefone', 11)->nullable();
            $table->string('telefone2', 11)->nullable();
            $table->string('observacoes', 50)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('hospedagens');
    }
};
