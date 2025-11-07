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
        Schema::table('imoveis', function (Blueprint $table) {
            // VIACEP
            $table->string('logradouro')->nullable()->after('endereco');
            $table->string('complemento_viacep')->nullable()->after('complemento');
            
            // TIPO PADRONIZADO
            $table->enum('tipo_enum', ['CS', 'AP', 'LT', 'GLP', 'PD', 'LJ', 'SL', 'OUTROS'])->default('OUTROS')->after('tipo');
            
            // AUDITORIA
            $table->dateTime('data_criacao')->useCurrent()->after('tipo_enum');
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
        Schema::table('imoveis', function (Blueprint $table) {
            $table->dropForeign(['usuario_criacao_id']);
            $table->dropForeign(['usuario_atualizacao_id']);
            
            $table->dropColumn([
                'logradouro',
                'complemento_viacep',
                'tipo_enum',
                'data_criacao',
                'usuario_criacao_id',
                'data_atualizacao',
                'usuario_atualizacao_id'
            ]);
        });
    }
};
