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
            // LOCALIZAÇÃO DE ATENDIMENTO
            $table->string('estado_atendimento')->nullable()->after('ativo');
            $table->string('cidade_atendimento')->nullable()->after('estado_atendimento');
            $table->string('bairro_atendimento')->nullable()->after('cidade_atendimento');
            
            // VALORES E AVALIAÇÃO
            $table->decimal('valor_hora', 10, 2)->default(0)->after('bairro_atendimento');
            $table->decimal('avaliacao', 3, 2)->default(0)->after('valor_hora'); // 0 a 5 stars
            
            // DOCUMENTOS
            $table->string('documento_numero')->nullable()->after('avaliacao');
            $table->string('documento_tipo')->default('CPF')->after('documento_numero'); // CPF, CNPJ
            
            // CONTATO
            $table->string('telefone_comercial')->nullable()->after('documento_tipo');
            $table->string('whatsapp')->nullable()->after('telefone_comercial');
            $table->string('email_comercial')->nullable()->after('whatsapp');
            
            // AUDITORIA
            $table->dateTime('data_criacao')->useCurrent()->after('email_comercial');
            $table->unsignedBigInteger('usuario_criacao_id')->nullable()->after('data_criacao');
            $table->dateTime('data_atualizacao')->useCurrent()->after('usuario_criacao_id');
            $table->unsignedBigInteger('usuario_atualizacao_id')->nullable()->after('data_atualizacao');
            
            // FOREIGN KEYS
            $table->foreign('usuario_criacao_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('usuario_atualizacao_id')->references('id')->on('users')->onDelete('set null');
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
            $table->dropForeign(['usuario_criacao_id']);
            $table->dropForeign(['usuario_atualizacao_id']);
            
            $table->dropColumn([
                'estado_atendimento',
                'cidade_atendimento',
                'bairro_atendimento',
                'valor_hora',
                'avaliacao',
                'documento_numero',
                'documento_tipo',
                'telefone_comercial',
                'whatsapp',
                'email_comercial',
                'data_criacao',
                'usuario_criacao_id',
                'data_atualizacao',
                'usuario_atualizacao_id'
            ]);
        });
    }
};
