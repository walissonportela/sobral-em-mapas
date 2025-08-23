<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Infrastructure\Models\Estatistica;

class EstatisticasController extends Controller
{
    public function registrar(Request $request)
    {
        Log::info("📩 [estatisticas] Payload recebido", ['all' => $request->all()]);

        // 1. Validação
        try {
            $validated = $request->validate([
                'session_id'               => 'required|string|max:6',
                'mapas_selecionados'       => 'required|array',
                'tempo_total'              => 'required|integer|min:0',
                'mapa_recomendado_por_mapa'=> 'nullable|array',
            ]);
            Log::info("✅ [estatisticas] Validação passou", $validated);
        } catch (\Throwable $e) {
            Log::error("❌ [estatisticas] Falha de validação", [
                'errors' => $e->getMessage(),
                'payload' => $request->all(),
            ]);
            throw $e;
        }

        $sessionId   = $validated['session_id'];
        $selecionados= $validated['mapas_selecionados'];
        $tempoTotal  = $validated['tempo_total'];
        $recomendados= $validated['mapa_recomendado_por_mapa'] ?? [];

        Log::info("🆔 Sessão {$sessionId} | ⏱ {$tempoTotal}s | Mapas recebidos", [
            'count' => count($selecionados),
            'mapas' => $selecionados
        ]);

        if (empty($selecionados)) {
            Log::warning("⚠️ [estatisticas] Nenhum mapa no payload", ['session' => $sessionId]);
            return response()->json([
                'message' => 'Nenhum mapa para registrar',
                'created_count' => 0,
                'created_ids' => [],
            ], 200);
        }

        $createdIds = [];
        $errors     = [];

        DB::beginTransaction();
        try {
            foreach ($selecionados as $mapa => $tempoMs) {
                $mapaRecomendado = $recomendados[$mapa] ?? null;

                Log::debug("➡️ [estatisticas] Tentando salvar", [
                    'mapa' => $mapa,
                    'tempoMs' => $tempoMs,
                    'mapaRecomendado' => $mapaRecomendado
                ]);

                try {
                    $stat = Estatistica::create([
                        'ip_usuario'       => $sessionId, // 🔎 confirme se a coluna é session_id ou ip_usuario
                        'mapa'             => (string) $mapa,
                        'tempo'            => (int) $tempoMs,
                        'mapa_recomendado' => $mapaRecomendado,
                        'tempo_total'      => (int) $tempoTotal,
                    ]);

                    Log::info("💾 [estatisticas] Registro criado", [
                        'id' => $stat->id ?? null,
                        'mapa' => $mapa
                    ]);

                    $createdIds[] = $stat->id ?? null;

                } catch (\Throwable $e) {
                    $errors[] = [
                        'mapa' => $mapa,
                        'error' => $e->getMessage()
                    ];
                    Log::error("❌ [estatisticas] Falha ao salvar mapa {$mapa}", [
                        'exception' => $e
                    ]);
                }
            }

            DB::commit();
            Log::info("✅ [estatisticas] Transação concluída", [
                'created_count' => count($createdIds),
                'errors' => $errors
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::critical("💥 [estatisticas] Erro geral na transação", ['exception' => $e]);

            return response()->json([
                'message' => 'Falha ao registrar estatísticas',
                'error'   => $e->getMessage(),
            ], 500);
        }

        $createdIds = array_values(array_filter($createdIds));

        return response()->json([
            'message'        => 'Estatísticas registradas com sucesso',
            'created_count'  => count($createdIds),
            'created_ids'    => $createdIds,
            'partial_errors' => $errors,
        ], 200);
    }
}
