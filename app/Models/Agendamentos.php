<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamentos extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';

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
}
