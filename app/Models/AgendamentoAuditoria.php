<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendamentoAuditoria extends Model
{
    use HasFactory;

    protected $table = 'agendamento_auditoria';
    
    protected $fillable = [
        'agendamento_id',
        'usuario_id',
        'acao',
        'campo_alterado',
        'valor_anterior',
        'valor_novo',
        'data_acao'
    ];

    protected $casts = [
        'data_acao' => 'datetime'
    ];

    // Relacionamentos
    public function agendamento()
    {
        return $this->belongsTo(Agendamentos::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Escopo para filtrar por agendamento
    public function scopeByAgendamento($query, $agendamentoId)
    {
        return $query->where('agendamento_id', $agendamentoId)
                     ->orderBy('data_acao', 'desc');
    }
}
