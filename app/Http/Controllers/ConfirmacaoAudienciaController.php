<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Processo;

class ConfirmacaoAudienciaController extends Controller
{
    /**
     * Confirma a audiência via link público (token).
     * Marca fl_audiencia_confirmada_pro = true no processo.
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function confirmar($token)
    {
        $processo = Processo::where('ds_confirmacao_audiencia_token_pro', $token)->first();
        if (!$processo) {
            return response()->view('confirmacao_audiencia.invalido', [], 404);
        }
        if ($processo->fl_audiencia_confirmada_pro) {
            return response()->view('confirmacao_audiencia.ja_confirmado');
        }
        $processo->fl_audiencia_confirmada_pro = true;
        $processo->save();
        return response()->view('confirmacao_audiencia.sucesso');
    }
}
