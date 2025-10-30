<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('operacoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('operacoes')->insert(
            [
                ['id'=> '1', 'nome'=>'Consórcio'],
                ['id'=> '2', 'nome'=>'CGI'],
                ['id'=> '3', 'nome'=>'Normal'],
                ['id'=> '4', 'nome'=>'CETIP Vencendo'],
                ['id'=> '5', 'nome'=>'Somente Vistoria'],
                ['id'=> '6', 'nome'=>'Piloto'],
                ['id'=> '7', 'nome'=>'Repasse'],
                ['id'=> '8', 'nome'=>'Particular'],
            ]
            );
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operacoes');
    }
};
