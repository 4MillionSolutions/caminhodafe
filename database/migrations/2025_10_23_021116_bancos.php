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
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->string('nome',150);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        DB::table('bancos')->insert([
            ['nome' => 'Itaú Unibanco', 'ativo' => true],
            ['nome' => 'Banco do Brasil', 'ativo' => true],
            ['nome' => 'Bradesco', 'ativo' => true],
            ['nome' => 'Caixa Econômica Federal', 'ativo' => true],
            ['nome' => 'Santander Brasil', 'ativo' => true],
            ['nome' => 'Nubank', 'ativo' => true],
            ['nome' => 'Banco Inter', 'ativo' => true],
            ['nome' => 'C6 Bank', 'ativo' => true],
            ['nome' => 'Banco Original', 'ativo' => true],
            ['nome' => 'Banco Safra', 'ativo' => true],
            ['nome' => 'BTG Pactual', 'ativo' => true],
            ['nome' => 'Banco Votorantim', 'ativo' => true],
            ['nome' => 'Banco PAN', 'ativo' => true],
            ['nome' => 'Banco Modal', 'ativo' => true],
            ['nome' => 'Banco Rendimento', 'ativo' => true],
            ['nome' => 'Banco Alfa', 'ativo' => true],
            ['nome' => 'Banco Banrisul', 'ativo' => true],
            ['nome' => 'Banco do Nordeste', 'ativo' => true],
            ['nome' => 'Banco da Amazônia', 'ativo' => true],
            ['nome' => 'Banco BMG', 'ativo' => true],
            ['nome' => 'Banco Daycoval', 'ativo' => true],
            ['nome' => 'Banco Mercantil do Brasil', 'ativo' => true],
            ['nome' => 'Banco Pine', 'ativo' => true],
            ['nome' => 'Banco Sofisa', 'ativo' => true],
            ['nome' => 'Banco UBS Brasil', 'ativo' => true],
            ['nome' => 'Banco HSBC Brasil', 'ativo' => true],
            ['nome' => 'Banco Citibank Brasil', 'ativo' => true],
            ['nome' => 'Banco Cooperativo do Brasil – Bancoob', 'ativo' => true],
            ['nome' => 'Sicoob', 'ativo' => true],
            ['nome' => 'Sicredi', 'ativo' => true],
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
