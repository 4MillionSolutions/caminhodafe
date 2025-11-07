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
        Schema::create('agendamento_servicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agendamento_id');
            $table->unsignedBigInteger('servico_id');
            $table->integer('prioridade_sla')->default(0);
            $table->timestamps();
            
            $table->foreign('agendamento_id')->references('id')->on('agendamentos')->onDelete('cascade');
            $table->foreign('servico_id')->references('id')->on('servicos')->onDelete('cascade');
            
            $table->unique(['agendamento_id', 'servico_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agendamento_servicos');
    }
};
