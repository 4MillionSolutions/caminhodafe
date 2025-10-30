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
        Schema::create('prestadores_regioes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestadores_id')->constrained('prestadores')->onDelete('cascade');
            $table->string('uf', 2)->nullable();
            $table->integer('raio')->nullable();
            $table->string('cidade', 150)->nullable();
            $table->string('longitude', 150)->nullable();
            $table->string('latitude', 150)->nullable();
            $table->float('valor', 11,2)->nullable();
            $table->longText('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('prestadores_regioes')->insert([
            [
                'prestadores_id' => 1,
                'uf' => '25',
                'raio' => '50',
                'cidade' => 'Arealva',
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
