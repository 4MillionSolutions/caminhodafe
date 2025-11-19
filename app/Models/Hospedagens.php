<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospedagens extends Model
{
    use HasFactory;

    protected $table = 'hospedagens';
    protected $fillable = [
        'nome',
        'menor_valor',
        'maior_valor',
        'valor_cafe',
        'desconto_parceiro',
        'parceiro',
        'email',
        'telefone',
        'telefone2',
        'observacoes',
        'ativo',
    ];
}
