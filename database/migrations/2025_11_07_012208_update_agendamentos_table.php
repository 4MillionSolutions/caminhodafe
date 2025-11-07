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
        Schema::table('agendamentos', function (Blueprint $table) {
            // OS INTERNA
            $table->string('os_interna')->unique()->nullable()->after('numero_sequencial');
            $table->string('os_plataforma')->nullable()->after('os_interna');
            
            // DATAS SLA
            $table->dateTime('data_criacao_demanda')->nullable()->after('os_plataforma');
            $table->dateTime('data_vencimento_sla')->nullable()->after('data_criacao_demanda');
            
            // DADOS DO AGENDAMENTO
            $table->string('contato_nome')->nullable()->after('numero_contato');
            $table->string('numero_contato_formatted')->nullable()->after('numero_contato');
            $table->text('observacao_externa')->nullable()->after('observacoes');
            
            // AUDITORIA - CRIAÇÃO
            $table->dateTime('data_criacao')->useCurrent()->after('observacao_externa');
            $table->unsignedBigInteger('usuario_criacao_id')->nullable()->after('data_criacao');
            
            // AUDITORIA - ATRIBUIÇÃO
            $table->dateTime('data_atribuicao')->nullable()->after('usuario_criacao_id');
            $table->unsignedBigInteger('usuario_atribuicao_id')->nullable()->after('data_atribuicao');
            
            // AUDITORIA - PRODUÇÃO
            $table->dateTime('data_producao')->nullable()->after('usuario_atribuicao_id');
            $table->unsignedBigInteger('usuario_producao_id')->nullable()->after('data_producao');
            
            // REFERÊNCIA PARA RETORNO/REAVALIAÇÃO
            $table->unsignedBigInteger('agendamento_referencia_id')->nullable()->after('usuario_producao_id');
            $table->enum('tipo_demanda_enum', ['ORIGINAL', 'RETORNO', 'REAVALIACAO'])->default('ORIGINAL')->after('agendamento_referencia_id');
            $table->enum('status', ['RASCUNHO', 'ATRIBUIDO', 'PRODUCAO', 'CONCLUIDO', 'REAGENDADO', 'RETORNO', 'REAVALIACAO', 'CANCELADO'])->default('RASCUNHO')->after('tipo_demanda_enum');
            
            // FOREIGN KEYS
            $table->foreign('usuario_criacao_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('usuario_atribuicao_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('usuario_producao_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('agendamento_referencia_id')->references('id')->on('agendamentos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropForeign(['usuario_criacao_id']);
            $table->dropForeign(['usuario_atribuicao_id']);
            $table->dropForeign(['usuario_producao_id']);
            $table->dropForeign(['agendamento_referencia_id']);
            
            $table->dropColumn([
                'os_interna',
                'os_plataforma',
                'data_criacao_demanda',
                'data_vencimento_sla',
                'contato_nome',
                'numero_contato_formatted',
                'observacao_externa',
                'data_criacao',
                'usuario_criacao_id',
                'data_atribuicao',
                'usuario_atribuicao_id',
                'data_producao',
                'usuario_producao_id',
                'agendamento_referencia_id',
                'tipo_demanda_enum',
                'status'
            ]);
        });
    }
};
