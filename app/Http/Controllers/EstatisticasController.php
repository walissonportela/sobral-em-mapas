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

   
        // Criar novo registro
        $estatistica = Estatistica::create([
            'ip_usuario' => $sessionId,
            'mapas_selecionados' => $novosMapas,
            'tempo_total' => $tempoTotal,
            'recommended_map_activations' => $recommendedActivations,
        ]);

        Log::info("🆕 Criando novo registro de estatística para o IP: $sessionId");

    return response()->json(['message' => 'Estatísticas registradas com sucesso'], 200);
}

}
