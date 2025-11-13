<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->decimal('deslocamento_valor', 10, 2)->nullable()->after('hora_fim');
            
            $table->text('deslocamento_observacoes')->nullable()->after('deslocamento_valor');
            
            $table->string('numero_demanda_interna')->nullable()->unique()->after('numero_sequencial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('deslocamento_valor');
            $table->dropColumn('deslocamento_observacoes');
            $table->dropColumn('numero_demanda_interna');
        });
    }
};
