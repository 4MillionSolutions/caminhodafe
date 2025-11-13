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
        Schema::table('servicos', function (Blueprint $table) {
            if (!Schema::hasColumn('servicos', 'sla')) {
                $table->integer('sla')->nullable()->default(5)->comment('SLA em dias');
            }
            if (!Schema::hasColumn('servicos', 'prioridade')) {
                $table->string('prioridade')->nullable()->comment('Prioridade do serviço');
            }
            if (!Schema::hasColumn('servicos', 'valor')) {
                $table->decimal('valor', 10, 2)->nullable()->comment('Valor do serviço');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dropColumn(['sla', 'prioridade', 'valor']);
        });
    }
};
