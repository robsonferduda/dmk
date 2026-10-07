<?php

namespace App\Http\Controllers;

use App\AutentiqueEvento;
use App\Jobs\ProcessarEventoAutentiqueJob;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Recebe os webhooks da Autentique: confere a assinatura HMAC, grava o evento (idempotente) e processa na fila.
 */
class AutentiqueWebhookController extends Controller
{
    public function receber(Request $request)
    {
        $corpo = $request->getContent();

        if (! self::assinaturaValida($corpo, (string) $request->header('X-Autentique-Signature'), (string) config('autentique.webhook_secret'))) {
            Log::warning('[autentique] Webhook com assinatura inválida recebido de ' . $request->ip());

            return response()->json(['erro' => 'assinatura inválida'], 401);
        }

        $payload = json_decode($corpo, true);
        if (! is_array($payload)) {
            return response()->json(['erro' => 'payload inválido'], 400);
        }

        $eventoId = (string) ($payload['event']['id'] ?? $payload['id'] ?? hash('sha256', $corpo));

        if (AutentiqueEvento::where('dc_evento_id_aev', $eventoId)->exists()) {
            return response()->json(['ok' => true, 'duplicado' => true]);
        }

        try {
            $evento = AutentiqueEvento::create([
                'dc_evento_id_aev'            => $eventoId,
                'dc_tipo_aev'                 => (string) ($payload['event']['type'] ?? ''),
                'cd_documento_autentique_aev' => self::documentoDoEvento($payload),
                'js_payload_aev'              => $corpo,
            ]);
        } catch (QueryException $e) {
            // Entrega simultânea do mesmo evento: a outra requisição já gravou.
            return response()->json(['ok' => true, 'duplicado' => true]);
        }

        ProcessarEventoAutentiqueJob::dispatch($evento->cd_autentique_evento_aev);

        return response()->json(['ok' => true]);
    }

    public static function assinaturaValida(string $corpo, string $assinatura, string $segredo): bool
    {
        if ($segredo === '' || $assinatura === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $corpo, $segredo), $assinatura);
    }

    /**
     * Id do documento: em eventos de documento vem no objeto; nos de assinatura, em data.document.
     */
    public static function documentoDoEvento(array $payload): ?string
    {
        $data = $payload['event']['data'] ?? [];
        $tipo = (string) ($payload['event']['type'] ?? '');

        $candidatos = [
            $data['document']['id'] ?? null,
            is_string($data['document'] ?? null) ? $data['document'] : null,
            strpos($tipo, 'document.') === 0 ? ($data['object']['id'] ?? $data['id'] ?? null) : null,
        ];

        foreach ($candidatos as $id) {
            if (is_string($id) && $id !== '') {
                return $id;
            }
        }

        return null;
    }
}
