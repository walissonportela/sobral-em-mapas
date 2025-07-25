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

        // 1. Validação dos dados
        $request->validate([
            'session_id' => 'required|string|max:6',
            'mapas_selecionados' => 'required|array',
            'tempo_total' => 'required|integer',
            'recommended_map_activations' => 'nullable|array',
            'mapa_anterior_por_recomendado' => 'nullable|array',
        ]);

        // 2. Dados do request
        $sessionId = $request->input('session_id');
        $mapasSelecionados = $request->input('mapas_selecionados');
        $tempoTotal = $request->input('tempo_total');
        $recommendedActivations = $request->input('recommended_map_activations', []);
        $mapaAnteriorPorRecomendado = $request->input('mapa_anterior_por_recomendado', []);

        Log::info("🆔 Sessão: $sessionId | ⏱ Tempo total: {$tempoTotal}s");

        // 3. Salvar uma linha para cada mapa
        foreach ($mapasSelecionados as $mapa => $tempo) {
            $recomendado = isset($recommendedActivations[$mapa]);
            $mapaAnterior = $recomendado ? ($mapaAnteriorPorRecomendado[$mapa] ?? null) : null;

            // Garante que seja string, se por acaso vier array
            if (is_array($mapaAnterior)) {
                $mapaAnterior = reset($mapaAnterior);
            }

            Log::info("🗺 Salvando Mapa: {$mapa} | Tempo: {$tempo}ms | Recomendado: " . ($recomendado ? 'sim' : 'não') . " | Mapa anterior: " . ($mapaAnterior ?? 'nenhum'));

            Estatistica::create([
                'ip_usuario' => $sessionId,
                'mapa' => $mapa,
                'tempo' => (int) $tempo,
                'recomendado' => $recomendado,
                'tempo_total' => $tempoTotal,
                'mapa_anterior' => $mapaAnterior,
            ]);
        }

        return response()->json(['message' => 'Estatísticas registradas com sucesso.'], 200);
    }
}
