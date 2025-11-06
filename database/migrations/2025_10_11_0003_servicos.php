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
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->integer('prioridade')->default(0);
            $table->integer('sla')->default(0);
            $table->decimal('valor', 10, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('servicos')->insert(
            [
                ['id'=> '1', 'nome'=>'Vistoria Bancária'],
                ['id'=> '2', 'nome'=>'Vistoria Particular'],
                ['id'=> '3', 'nome'=>'Vistoria de Obra'],
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
        Schema::dropIfExists('servicos');
    }
};
