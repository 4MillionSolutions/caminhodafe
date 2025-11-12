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
        Schema::table('prestadores', function (Blueprint $table) {
            $table->string('estado_atendimento', 2)->nullable()->after('localizacao');
            $table->string('cidade_atendimento', 100)->nullable()->after('estado_atendimento');
            $table->string('bairro_atendimento', 100)->nullable()->after('cidade_atendimento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prestadores', function (Blueprint $table) {
            $table->dropColumn(['estado_atendimento', 'cidade_atendimento', 'bairro_atendimento']);
        });
    }
};
