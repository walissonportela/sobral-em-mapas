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

        // 1. Validação dos dados esperados
        $request->validate([
            'session_id' => 'required|string|max:6',
            'mapas_selecionados' => 'required|array',
            'tempo_total' => 'required|integer',
            'mapa_recomendado_por_mapa' => 'nullable|array',
        ]);

        // 2. Dados do request
        $sessionId = $request->input('session_id');
        $mapasSelecionados = $request->input('mapas_selecionados');
        $tempoTotal = $request->input('tempo_total');
        $recomendados = $request->input('mapa_recomendado_por_mapa', []);

        Log::info("🆔 Sessão: $sessionId | ⏱ Tempo total: {$tempoTotal}s");

        // 3. Salvar uma linha por mapa ativado
        foreach ($mapasSelecionados as $mapa => $tempo) {
            $mapaRecomendado = $recomendados[$mapa] ?? null;

            Log::info("🗺 Salvando Mapa: {$mapa} | Tempo: {$tempo}ms | Recomendou: " . ($mapaRecomendado ?? 'nenhum'));

            Estatistica::create([
                'ip_usuario' => $sessionId,
                'mapa' => $mapa,
                'tempo' => (int) $tempo,
                'mapa_recomendado' => $mapaRecomendado,
                'tempo_total' => $tempoTotal,
            ]);
        }

        return response()->json(['message' => 'Estatísticas registradas com sucesso.'], 200);
    }
}
