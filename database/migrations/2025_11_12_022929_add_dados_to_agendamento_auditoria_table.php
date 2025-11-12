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
        Schema::table('agendamento_auditoria', function (Blueprint $table) {
            $table->text('dados_anteriores')->nullable()->after('acao');
            $table->text('dados_novos')->nullable()->after('dados_anteriores');
            $table->string('descricao')->nullable()->after('dados_novos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agendamento_auditoria', function (Blueprint $table) {
            $table->dropColumn(['dados_anteriores', 'dados_novos', 'descricao']);
        });
    }
};
