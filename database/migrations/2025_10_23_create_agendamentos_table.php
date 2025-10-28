<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_sequencial', 20)->unique();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('imovel_id')->nullable();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->unsignedBigInteger('prestador_id')->nullable();
            $table->string('tipo_demanda', 50)->nullable();
            $table->string('numero_proposta', 50)->nullable();
            $table->string('numero_contato', 50)->nullable();
            $table->string('os_numero')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('arquivo_matricula')->nullable();
            $table->string('arquivo_iptu')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('imovel_id')->references('id')->on('imoveis')->onDelete('set null');
            $table->foreign('prestador_id')->references('id')->on('prestadores')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agendamentos');
    }
};
