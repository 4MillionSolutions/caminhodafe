<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendamentoServico extends Model
{
    use HasFactory;

    protected $table = 'agendamento_servicos';

    protected $fillable = [
        'agendamento_id',
        'servico_id',
        'prioridade_sla'
    ];

    // Timestamps não são necessários nesta tabela de junção
    public $timestamps = false;

    // Relacionamentos
    public function agendamento()
    {
        return $this->belongsTo(Agendamentos::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servicos::class);
    }
}
