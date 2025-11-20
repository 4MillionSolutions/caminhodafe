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
        Schema::create('peregrinos', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('endereco', 200)->nullable();
            $table->integer('numero')->length(11)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('bairro', 150)->nullable();
            $table->string('cidade', 150)->nullable();
            $table->string('estado', 40)->nullable();
            $table->string('telefone', 11);
            $table->string('email', 100);
            $table->string('status',1);
            $table->timestamps();
        });
        DB::table('peregrinos')->insert([
            [
                'id' => 1,
                'nome' => 'Admin',
                'email' => 'admin@admin.com',
                'telefone' => '11999999999',
                'status' => 'A'
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
        Schema::dropIfExists('peregrinos');
    }
};
