<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Infrastructure\Models\Estatistica;
use Illuminate\Support\Facades\Log;

class EstatisticasController extends Controller
{
    public function registrar(Request $request)
{
    Log::info("📩 Recebendo estatísticas...", ['request' => $request->all()]);

    // Valida os dados recebidos
    $request->validate([
        'session_id' => 'required|string|max:6',
        'mapas_selecionados' => 'required|array',
        'tempo_total' => 'required|integer',
        'recommended_map_activations' => 'nullable|array', // Agora aceita {} ou []
    ]);

    $sessionId = $request->input('session_id');
    $novosMapas = $request->input('mapas_selecionados');
    $tempoTotal = $request->input('tempo_total');
    $recommendedActivations = $request->input('recommended_map_activations');

    Log::info("🆔 ID da Sessão: $sessionId");
    Log::info("🗺 Mapas Selecionados:", $novosMapas);
    Log::info("⏳ Tempo total na página: {$tempoTotal} segundos");
    Log::info("🔥 Recomendações ativadas:", $recommendedActivations);

    // Busca o registro existente pelo ID da sessão
    $estatistica = Estatistica::where('ip_usuario', $sessionId)->first();

    if ($estatistica) {
        // Atualiza mapas selecionados
        $mapasAtuais = is_string($estatistica->mapas_selecionados) 
            ? json_decode($estatistica->mapas_selecionados, true) 
            : $estatistica->mapas_selecionados;

        foreach ($novosMapas as $mapa => $contador) {
            $mapasAtuais[$mapa] = ($mapasAtuais[$mapa] ?? 0) + $contador;
        }

        // Atualiza ativações de mapas recomendados
        $recommendedAtuais = is_string($estatistica->recommended_map_activations)
            ? json_decode($estatistica->recommended_map_activations, true)
            : $estatistica->recommended_map_activations;

        foreach ($recommendedActivations as $mapa => $contador) {
            $recommendedAtuais[$mapa] = ($recommendedAtuais[$mapa] ?? 0) + $contador;
        }

        // Atualiza o banco de dados
        $estatistica->update([
            'mapas_selecionados' => $mapasAtuais,
            'tempo_total' => $estatistica->tempo_total + $tempoTotal,
            'recommended_map_activations' => $recommendedAtuais,
        ]);

        Log::info("✅ Estatística atualizada com sucesso para $sessionId.");
    } else {
        // Criar novo registro
        $estatistica = Estatistica::create([
            'ip_usuario' => $sessionId,
            'mapas_selecionados' => $novosMapas,
            'tempo_total' => $tempoTotal,
            'recommended_map_activations' => $recommendedActivations,
        ]);

        Log::info("🆕 Criando novo registro de estatística para o IP: $sessionId");
    }

    return response()->json(['message' => 'Estatísticas registradas com sucesso'], 200);
}

}
