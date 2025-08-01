<?php

namespace App\Infrastructure\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estatistica extends Model
{
    use HasFactory;

    protected $table = 'estatisticas';

       protected $fillable = [
        'ip_usuario',
        'mapa',
        'tempo',
        'tempo_total',
        'mapa_recomendado',
    ];


    protected $casts = [
        'mapas_selecionados' => 'array',
        'recommended_map_activations' => 'array', // Convertendo para JSON automaticamente
    ];
}
