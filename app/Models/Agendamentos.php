<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamentos extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';

    protected $fillable = [
        'cliente_id',
        'imovel_id',
        'prestador_id',
        'servico_id',
        'tecnico_id',
        'tipo_demanda_id',
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
        'status',
        'deslocamento_valor',
        'deslocamento_observacoes',
        'numero_demanda_interna',
        'hora_inicio',
        'hora_fim'
    ];

    protected $casts = [
        'data_agendamento' => 'datetime',
        'data_criacao' => 'datetime',
        'data_atribuicao' => 'datetime',
        'data_producao' => 'datetime',
        'data_criacao_demanda' => 'datetime',
        'data_vencimento_sla' => 'datetime',
        'deslocamento_valor' => 'decimal:2'
    ];

    // Relacionamentos
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function imovel()
    {
        return $this->belongsTo(Imoveis::class, 'imovel_id');
    }

    public function prestador()
    {
        return $this->belongsTo(Prestadores::class, 'prestador_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(Prestadores::class, 'tecnico_id');
    }

    public function tipoDemanda()
    {
        return $this->belongsTo(Motivos::class, 'tipo_demanda_id');
    }

    public function servico()
    {
        return $this->belongsTo(Servicos::class, 'servico_id');
    }

    // Novos relacionamentos para auditoria e rastreamento
    public function usuarioCriacao()
    {
        return $this->belongsTo(User::class, 'usuario_criacao_id');
    }

    public function usuarioAtribuicao()
    {
        return $this->belongsTo(User::class, 'usuario_atribuicao_id');
    }

    public function usuarioProducao()
    {
        return $this->belongsTo(User::class, 'usuario_producao_id');
    }

    public function auditoria()
    {
        return $this->hasMany(AgendamentoAuditoria::class);
    }

    public function servicos()
    {
        return $this->belongsToMany(Servicos::class, 'agendamento_servicos')
                    ->withPivot('prioridade_sla');
    }

    public function agendamentoServicos()
    {
        return $this->hasMany(AgendamentoServico::class);
    }

    public function agendamentoReferenciado()
    {
        return $this->belongsTo(Agendamentos::class, 'agendamento_referencia_id');
    }

    public function agendamentosReferenciantes()
    {
        return $this->hasMany(Agendamentos::class, 'agendamento_referencia_id');
    }

    // Métodos úteis
    public function registrarAuditoria($acao, $usuarioId, $campoAlterado = null, $valorAnterior = null, $valorNovo = null)
    {
        return $this->auditoria()->create([
            'usuario_id' => $usuarioId,
            'acao' => $acao,
            'campo_alterado' => $campoAlterado,
            'valor_anterior' => $valorAnterior,
            'valor_novo' => $valorNovo,
            'data_acao' => now()
        ]);
    }

    public function gerarOsInterna()
    {
        if ($this->os_interna) {
            return $this->os_interna;
        }

        // Encontra o último número de OS
        $ultimoOs = Agendamentos::where('os_interna', 'LIKE', 'OS-%')
            ->orderBy('id', 'desc')
            ->first();

        $numero = 1;
        if ($ultimoOs && preg_match('/OS-(\d+)/', $ultimoOs->os_interna, $matches)) {
            $numero = (int)$matches[1] + 1;
        }

        $novaOs = 'OS-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
        $this->update(['os_interna' => $novaOs]);

        return $novaOs;
    }

    // Escopo para filtrar por status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Escopo para filtrar por prestador
    public function scopeByPrestador($query, $prestadorId)
    {
        return $query->where('prestador_id', $prestadorId);
    }

    // Escopo para filtrar por cliente
    public function scopeByCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    // Escopo para filtrar por imóvel
    public function scopeByImovel($query, $imovelId)
    {
        return $query->where('imovel_id', $imovelId);
    }

    // Escopo ordenar por data de vencimento SLA
    public function scopeOrdenadoSLA($query)
    {
        return $query->orderBy('data_vencimento_sla', 'asc');
    }
}
