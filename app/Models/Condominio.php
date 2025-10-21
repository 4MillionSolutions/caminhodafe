<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condominio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'area_terreno',
        'area_privativa',
        'laudo_id',
    ];

    protected $casts = [
        'area_terreno' => 'decimal:2',
        'area_privativa' => 'decimal:2',
    ];

    // Relacionamento com laudo
    public function laudo()
    {
        return $this->belongsTo(Laudo::class);
    }
}
