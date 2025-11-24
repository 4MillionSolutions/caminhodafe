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
        Schema::create('cidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('status',1);
            $table->timestamps();
        });

        // 1  => 'Águas da Prata',
        //     2  => 'Andradas',
        //     3  => 'Serra dos Lima',
        //     4  => 'Barra',
        //     5  => 'Crisólia',
        //     6  => 'Ouro Fino',
        //     7  => 'Inconfidentes',
        //     8  => 'Borda da Mata',
        //     9  => 'Tocos do Moji',
        //     10 => 'Estiva',
        //     11 => 'Consolação',
        //     12 => 'Paraisópolis',
        //     13 => 'Canta Galo',
        //     14 => 'Luminosa',
        //     15 => 'Campista',
        //     16 => 'Campos do Jordão',
        //     17 => 'Piracuama',
        //     18 => 'Distrito Mandu',
        //     19 => 'Pindamonhangaba',
        //     20 => 'Roseira',
        //     21 => 'Aparecida',
        //     22 => 'Pedrinha',
        //     23 => 'Gomeral',
        //     24 => 'Estrada do Paiol Grande',
        //     25 => 'Taguá'

        DB::table('cidades')->insert([
            [ 'id' => 1, 'nome' => 'Águas da Prata', 'status' => 'A'],
            [ 'id' => 2, 'nome' => 'Andradas', 'status' => 'A'],
            [ 'id' => 3, 'nome' => 'Serra dos Lima', 'status' => 'A'],
            [ 'id' => 4, 'nome' => 'Barra', 'status' => 'A'],
            [ 'id' => 5, 'nome' => 'Crisólia', 'status' => 'A'],
            [ 'id' => 6, 'nome' => 'Ouro Fino', 'status' => 'A'],
            [ 'id' => 7, 'nome' => 'Inconfidentes', 'status' => 'A'],
            [ 'id' => 8, 'nome' => 'Borda da Mata', 'status' => 'A'],
            [ 'id' => 9, 'nome' => 'Tocos do Moji', 'status' => 'A'],
            [ 'id' => 10, 'nome' => 'Estiva', 'status' => 'A'],
            [ 'id' => 11, 'nome' => 'Consolação', 'status' => 'A'],
            [ 'id' => 12, 'nome' => 'Paraisópolis', 'status' => 'A'],
            [ 'id' => 13, 'nome' => 'Canta Galo', 'status' => 'A'],
            [ 'id' => 14, 'nome' => 'Luminosa', 'status' => 'A'],
            [ 'id' => 15, 'nome' => 'Campista', 'status' => 'A'],
            [ 'id' => 16, 'nome' => 'Campos do Jordão', 'status' => 'A'],
            [ 'id' => 17, 'nome' => 'Piracuama', 'status' => 'A'],
            [ 'id' => 18, 'nome' => 'Distrito Mandu', 'status' => 'A'],
            [ 'id' => 19, 'nome' => 	'Pindamonhangaba',		'status'	=>	'A'],
            [	'id'	=>	20,	'nome'	=>	'Roseira',		'status'	=>	'A'],
            [	'id'    =>	21,	'nome'	=>	'Aparecida',		'status'	=>	'A'],
            [	'id'    =>	22,	'nome'	=>	'Pedrinha',		'status'	=>	'A'],
            [	'id'    =>	23,	'nome'	=>	'Gomeral',		'status'	=>	'A'],
            [	'id'    =>	24,	'nome'	=>	'Estrada do Paiol Grande',		'status'	=>	'A'],
            [	'id'    =>	25,	'nome'	=>	'Taguá',		'status'	=>	'A'],
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
