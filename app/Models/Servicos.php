<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicos extends Model
{
    use HasFactory;

    protected $table = 'servicos';

    protected $fillable = [
        'nome',
        'prioridade',
        'sla',
        'valor',
        'ativo'
    ];

    public function agendamentos()
    {
        return $this->hasMany(Agendamentos::class, 'servico_id');
    }
}
