<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestadoresRegioes extends Model
{
    use HasFactory;

    protected $table = 'prestadores_regioes';

    protected $fillable = [
        'prestadores_id',
        'uf',
        'raio',
        'cidade',
        'longitude',
        'latitude',
        'valor',
        'observacoes',
        'ativo'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'valor' => 'float',
        'raio' => 'integer',
        'ativo' => 'boolean'
    ];

    // Relacionamento com prestador
    public function prestador()
    {
        return $this->belongsTo(Prestadores::class, 'prestadores_id');
    }

    /**
     * Escopo para buscar prestadores ativos em uma região por UF e cidade
     */
    public function scopeEmRegiao($query, $estado, $cidade)
    {
        return $query->where('uf', $estado)
                     ->where('cidade', $cidade)
                     ->where('ativo', true);
    }

    /**
     * Escopo para buscar prestadores ordenados por distância
     * Usa fórmula de Haversine para calcular distância entre dois pontos (em km)
     * 
     * @param $latitude - Latitude do ponto de origem
     * @param $longitude - Longitude do ponto de origem
     */
    public function scopePorProximidade($query, $latitude, $longitude)
    {
        return $query->selectRaw(
            'prestadores_regioes.*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(CAST(latitude AS DECIMAL(10,8)))) *
                cos(radians(CAST(longitude AS DECIMAL(11,8))) - radians(?)) +
                sin(radians(?)) * sin(radians(CAST(latitude AS DECIMAL(10,8))))
            )) AS distancia_km',
            [$latitude, $longitude, $latitude]
        )
        ->where('ativo', true)
        ->orderBy('distancia_km', 'asc');
    }

    /**
     * Escopo para buscar prestadores dentro de um raio específico
     * 
     * @param $latitude - Latitude do ponto de origem
     * @param $longitude - Longitude do ponto de origem
     * @param $raioKm - Raio em quilômetros (padrão: 50)
     */
    public function scopePorRaio($query, $latitude, $longitude, $raioKm = 50)
    {
        return $query->selectRaw(
            'prestadores_regioes.*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(CAST(latitude AS DECIMAL(10,8)))) *
                cos(radians(CAST(longitude AS DECIMAL(11,8))) - radians(?)) +
                sin(radians(?)) * sin(radians(CAST(latitude AS DECIMAL(10,8))))
            )) AS distancia_km',
            [$latitude, $longitude, $latitude]
        )
        ->whereRaw(
            '(6371 * acos(
                cos(radians(?)) * cos(radians(CAST(latitude AS DECIMAL(10,8)))) *
                cos(radians(CAST(longitude AS DECIMAL(11,8))) - radians(?)) +
                sin(radians(?)) * sin(radians(CAST(latitude AS DECIMAL(10,8))))
            )) <= ?',
            [$latitude, $longitude, $latitude, $raioKm]
        )
        ->where('ativo', true)
        ->orderBy('distancia_km', 'asc');
    }

    /**
     * Escopo para ordenar por valor de deslocamento (melhor valor primeiro)
     */
    public function scopePorValor($query)
    {
        return $query->orderBy('valor', 'asc');
    }

    /**
     * Escopo para ordenar por avaliação do prestador (melhor avaliação primeiro)
     */
    public function scopePorAvaliacao($query)
    {
        return $query->join('prestadores', 'prestadores_regioes.prestadores_id', '=', 'prestadores.id')
                     ->orderBy('prestadores.avaliacao', 'desc');
    }

    /**
     * Escopo para ativos apenas
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}

