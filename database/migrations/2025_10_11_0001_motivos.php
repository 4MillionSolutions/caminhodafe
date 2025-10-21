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
        Schema::create('motivos_chamados', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('motivos_chamados')->insert(
            [
                ['id'=> '1', 'nome'=>'Avaliação'],
                ['id'=> '2', 'nome'=>'Reavaliação'],
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
        Schema::dropIfExists('motivos_chamados');
    }
};
