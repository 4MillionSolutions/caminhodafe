<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laudo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente',
        'proposta',
        'cetip',
        'hor_homo',
        'hor_foto',
        'tipo',
        'operacao',
        'endereco',
        'bairro',
        'cidade',
        'estado',
        'data_vistoria',
        'hora_vistoria',
        'resp_vistoria',
        'crea_cau',
        'resp_laudo',
        'status',
        'hora_envio',
        'env',
        'remarcado',
        'enviado_analise',
        'enviado_cliente',
    ];

    protected $casts = [
        'data_vistoria' => 'date',
        'hor_homo' => 'datetime:H:i:s',
        'hor_foto' => 'datetime:H:i:s',
        'hora_vistoria' => 'datetime:H:i:s',
        'hora_envio' => 'datetime:H:i:s',
        'remarcado' => 'boolean',
        'enviado_analise' => 'boolean',
        'enviado_cliente' => 'boolean',
    ];

    public function condominios()
    {
        return $this->hasMany(Condominio::class);
    }

    public function scopePorCliente($query, $cliente)
    {
        return $query->where('cliente', 'like', "%{$cliente}%");
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorData($query, $data)
    {
        return $query->whereDate('data_vistoria', $data);
    }
    
    public function getStatusCalculadoAttribute()
    {
        if (empty($this->resp_vistoria)) {
            return "Falta estagiário!";
        }
        
        if ($this->remarcado) {
            return "Remarcar Vistoria!";
        }
        
        if (empty($this->resp_laudo)) {
            return "Falta responsável do laudo!";
        }
        
        if (empty($this->status)) {
            return "Laudo em confecção!";
        }
        
        if (empty($this->data_vistoria)) {
            return "Falta vistoria!";
        }
        
        if (!str_ends_with($this->status, 'ok')) {
            return "Laudo em lançamento!";
        }
        
        if (!$this->enviado_analise) {
            return "Enviado para análise!";
        }
        
        return "Enviado para o cliente!";
    }
}
