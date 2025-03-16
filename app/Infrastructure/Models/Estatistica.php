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
        'mapas_selecionados',
        'tempo_total',
        'recommended_map_activations', // Adicionando a nova coluna
    ];

    protected $casts = [
        'mapas_selecionados' => 'array',
        'recommended_map_activations' => 'array', // Convertendo para JSON automaticamente
    ];
}
