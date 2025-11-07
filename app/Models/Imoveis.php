<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imoveis extends Model
{
    use HasFactory;

    protected $table = 'imoveis';

    protected $fillable = [
        'cliente_id',
        'cep',
        'estado',
        'cidade',
        'bairro',
        'endereco',
        'numero',
        'logradouro',
        'complemento_viacep',
        'tipo_enum',
        'data_criacao',
        'usuario_criacao_id',
        'data_atualizacao',
        'usuario_atualizacao_id'
    ];

    protected $casts = [
        'data_criacao' => 'datetime',
        'data_atualizacao' => 'datetime'
    ];

    // Relacionamentos
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamentos::class, 'imovel_id');
    }

    public function usuarioCriacao()
    {
        return $this->belongsTo(User::class, 'usuario_criacao_id');
    }

    public function usuarioAtualizacao()
    {
        return $this->belongsTo(User::class, 'usuario_atualizacao_id');
    }

    public function prestadores()
    {
        return $this->belongsToMany(Prestadores::class, 'prestador_imovel');
    }

    // Escopo para filtrar por CEP
    public function scopeByCep($query, $cep)
    {
        return $query->where('cep', $cep);
    }

    // Escopo para filtrar por localização
    public function scopeByLocalizacao($query, $estado, $cidade, $bairro = null)
    {
        return $query->where('estado', $estado)
                     ->where('cidade', $cidade)
                     ->when($bairro, function ($q) use ($bairro) {
                         $q->where('bairro', $bairro);
                     });
    }

    // Escopo para filtrar por cliente
    public function scopeByCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    // Método para obter endereço completo formatado
    public function getEnderecoCompletoAttribute()
    {
        return sprintf(
            '%s, %s %s - %s, %s - %s',
            $this->logradouro ?: $this->endereco,
            $this->numero,
            $this->complemento_viacep ?: '',
            $this->bairro,
            $this->cidade,
            $this->estado
        );
    }
}
