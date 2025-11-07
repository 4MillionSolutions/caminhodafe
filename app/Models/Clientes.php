<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $dates = []; // desativa o cast automático

    public function getCreatedAtAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y H:i:s') : null;
    }
}
