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
        Schema::create('prestadores_regioes_servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestadores_id')->constrained('prestadores')->onDelete('cascade');
            $table->foreignId('servicos_id')->constrained('servicos')->onDelete('cascade');
            $table->foreignId('prestadores_regioes_id')->constrained('prestadores_regioes')->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('prestadores_regioes_servicos')->insert([
            [
                'prestadores_id' => 1,
                'servicos_id' => 1,
                'prestadores_regioes_id' => 1
            ]
        ]);
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
