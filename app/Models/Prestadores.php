<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestadores extends Model
{
    use HasFactory;

    protected $table = 'prestadores';

    protected $fillable = [
        'nome',
        'empresa',
        'contato',
        'email',
        'telefone',
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
    ];

    protected $casts = [
        'valor_hora' => 'decimal:2',
        'avaliacao' => 'decimal:2',
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime'
    ];

    // Relacionamentos
    public function agendamentos()
    {
        return $this->hasMany(Agendamentos::class, 'prestador_id');
    }

    public function agendamentosComoPrestador()
    {
        return $this->hasMany(Agendamentos::class, 'prestador_id');
    }

    public function agendamentosComoTecnico()
    {
        return $this->hasMany(Agendamentos::class, 'tecnico_id');
    }

    public function usuarioCriacao()
    {
        return $this->belongsTo(User::class, 'usuario_criacao_id');
    }

    public function usuarioAtualizacao()
    {
        return $this->belongsTo(User::class, 'usuario_atualizacao_id');
    }

    public function imoveis()
    {
        return $this->belongsToMany(Imoveis::class, 'prestador_imovel');
    }

    public function servicos()
    {
        return $this->belongsToMany(Servicos::class, 'prestador_servicos');
    }

    // Escopo para filtrar por localização de atendimento
    public function scopeByLocalizacaoAtendimento($query, $estado, $cidade, $bairro = null)
    {
        return $query->where('estado_atendimento', $estado)
                     ->where('cidade_atendimento', $cidade)
                     ->when($bairro, function ($q) use ($bairro) {
                         $q->where('bairro_atendimento', $bairro);
                     });
    }

    // Escopo para filtrar por valor/hora máximo
    public function scopeByValorHoraMaximo($query, $valorMaximo)
    {
        return $query->where('valor_hora', '<=', $valorMaximo)
                     ->orWhereNull('valor_hora');
    }

    // Escopo para filtrar por avaliação mínima
    public function scopeByAvaliacaoMinima($query, $avaliacaoMinima)
    {
        return $query->where('avaliacao', '>=', $avaliacaoMinima)
                     ->orWhereNull('avaliacao');
    }

    // Escopo para ordenar por valor/hora (crescente)
    public function scopeOrdenadoValor($query)
    {
        return $query->orderBy('valor_hora', 'asc');
    }

    // Escopo para ordenar por avaliação (decrescente)
    public function scopeOrdenadoAvaliacao($query)
    {
        return $query->orderBy('avaliacao', 'desc');
    }

    // Escopo para ativos
    public function scopeAtivos($query)
    {
        return $query->whereNotNull('nome')
                     ->whereNotNull('email')
                     ->whereNotNull('telefone');
    }

    // Método para obter nome completo
    public function getNomeCompletoAttribute()
    {
        if ($this->empresa) {
            return $this->nome . ' - ' . $this->empresa;
        }
        return $this->nome;
    }

    // Método para obter localização formatada
    public function getLocalizacaoAtendimentoAttribute()
    {
        if (!$this->bairro_atendimento) {
            return $this->cidade_atendimento . ', ' . $this->estado_atendimento;
        }
        return $this->bairro_atendimento . ' - ' . $this->cidade_atendimento . ', ' . $this->estado_atendimento;
    }
}
