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
        Schema::create('laudos', function (Blueprint $table) {
            $table->id();
            
            // Campos baseados no Excel - Tabela Principal
            $table->string('cliente')->nullable();
            $table->string('proposta')->nullable();
            $table->string('cetip')->nullable();
            $table->time('hor_homo')->nullable(); // Horário Homologação
            $table->time('hor_foto')->nullable(); // Horário Foto
            $table->string('tipo')->nullable();
            $table->string('operacao')->nullable();
            $table->text('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->date('data_vistoria')->nullable();
            $table->time('hora_vistoria')->nullable();
            $table->string('resp_vistoria')->nullable();
            $table->string('crea_cau')->nullable();
            $table->string('resp_laudo')->nullable();
            $table->string('status')->nullable();
            $table->time('hora_envio')->nullable();
            
            // Campos de controle
            $table->string('env')->nullable();
            $table->boolean('remarcado')->default(false);
            $table->boolean('enviado_analise')->default(false);
            $table->boolean('enviado_cliente')->default(false);
            
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
        Schema::dropIfExists('laudos');
    }
};
