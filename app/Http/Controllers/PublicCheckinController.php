<?php

namespace App\Http\Controllers;

use App\Processo;
use App\ProcessoCheckin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * [CHECK-IN PÚBLICO]
 * Permite ao correspondente registrar o check-in de um processo SEM login,
 * acessando uma URL com token: /c/{token}
 *
 * Token é gerado e persistido em processo_pro.ds_checkin_token_pro
 * (rota única por processo). O token é o único segredo: quem o tiver pode
 * fazer o check-in daquele processo.
 *
 * Limitações/escopo:
 *  - 1 check-in ativo por processo (mesma constraint do fluxo logado).
 *  - GPS coletado pelo navegador (HTML5 Geolocation).
 *  - Sem upload de foto.
 */
class PublicCheckinController extends Controller
{
    /**
     * Exibe a página de check-in (sem login).
     */
    public function show($token)
    {
        $processo = $this->processoPorToken($token);
        if (!$processo) {
            return response()->view('checkin-publico.invalido', [], 404);
        }

        $processo->load('cliente', 'vara', 'cidade.estado', 'correspondente');

        $checkin = ProcessoCheckin::where('cd_processo_pro', $processo->cd_processo_pro)->first();

        return view('checkin-publico.formulario', [
            'processo' => $processo,
            'token'    => $token,
            'checkin'  => $checkin,
        ]);
    }

    /**
     * Recebe o POST com lat/lng/precisão e registra o check-in.
     */
    public function store(Request $request, $token)
    {
        $processo = $this->processoPorToken($token);
        if (!$processo) {
            return response()->json(['success' => false, 'message' => 'Link inválido ou expirado.'], 404);
        }

        $validator = \Validator::make($request->all(), [
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'precisao'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . implode(' | ', $validator->errors()->all()),
            ], 422);
        }

        $existente = ProcessoCheckin::where('cd_processo_pro', $processo->cd_processo_pro)->first();
        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'Check-in já registrado em '
                              . \Carbon\Carbon::parse($existente->dt_checkin_pck)->format('d/m/Y H:i') . '.',
                'checkin' => $existente,
            ], 409);
        }

        try {
            $ck = ProcessoCheckin::create([
                'cd_processo_pro'        => $processo->cd_processo_pro,
                'cd_conta_con'           => $processo->cd_conta_con,
                'cd_correspondente_cor'  => $processo->cd_correspondente_cor,
                // Sem usuário autenticado: deixamos os campos de auditoria
                // dependentes de Auth como NULL. A coluna nu_telefone do
                // remetente do WhatsApp pode ser cruzada via webhook depois.
                'cd_user_checkin_pck'    => null,
                'cd_entidade_ete'        => null,
                'dt_checkin_pck'         => date('Y-m-d H:i:s'),
                'nu_latitude_pck'        => $request->input('latitude'),
                'nu_longitude_pck'       => $request->input('longitude'),
                'nu_precisao_metros_pck' => $request->input('precisao'),
                'ds_endereco_pck'        => $request->input('endereco'),
                'ds_observacao_pck'      => 'Check-in via link público (WhatsApp).',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23505') {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in já registrado para este processo.',
                ], 409);
            }
            Log::error('[CHECKIN-PUBLICO] erro: ' . $e->getMessage());
            throw $e;
        }

        // [CHECK-IN PÚBLICO] Token de uso único: invalida após o check-in.
        // Se o correspondente reabrir o link, verá "link inválido". A
        // tela de sucesso continua aparecendo nesta resposta JSON.
        $processo->ds_checkin_token_pro = null;
        $processo->fl_checkin_pro = true;
        $processo->save();

        // Notifica o escritório (WhatsApp), fire-and-forget.
        $cdCheckin = $ck->cd_processo_checkin_pck;
        app()->terminating(function () use ($cdCheckin) {
            \App\Services\Checkin\CheckinNotifier::notificar($cdCheckin);
        });

        return response()->json(['success' => true, 'checkin' => $ck]);
    }

    /**
     * Localiza o processo pelo token (não trashado).
     */
    private function processoPorToken($token)
    {
        if (empty($token) || !preg_match('/^[A-Za-z0-9]{16,80}$/', $token)) {
            return null;
        }
        return Processo::where('ds_checkin_token_pro', $token)->first();
    }
}
