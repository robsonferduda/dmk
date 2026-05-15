<?php

namespace App\Http\Controllers;

use Auth;
use App\Processo;
use App\ProcessoCheckin;
use Illuminate\Http\Request;

/**
 * [CHECK-IN]
 * Controller dos check-ins do correspondente em um processo.
 *
 * Espelha o ProcessoComprovacaoController (Timemark), porém SEM foto:
 * apenas registra data/hora + GPS quando o correspondente "chega no fórum".
 *
 * Regra: 1 (um) check-in ativo por processo. A constraint UNIQUE parcial
 * em processo_checkin_pck (deleted_at IS NULL) garante isso no banco.
 */
class ProcessoCheckinController extends Controller
{
    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    /**
     * Lista (JSON) de check-ins de um processo.
     */
    public function index($id)
    {
        $processo = $this->processoAcessivel($id);
        if (!$processo) {
            return response()->json(['success' => false, 'message' => 'Processo não acessível'], 403);
        }

        $itens = ProcessoCheckin::where('cd_processo_pro', $processo->cd_processo_pro)
                                 ->orderBy('dt_checkin_pck', 'DESC')
                                 ->get();

        return response()->json($itens);
    }

    /**
     * Registra o check-in (lat/lng/precisão + observação opcional).
     *
     * Espera application/json ou form-urlencoded:
     *   - latitude, longitude, precisao (opcionais)
     *   - dt_checkin (ISO 8601, opcional - default: now)
     *   - endereco (opcional)
     *   - observacao (opcional)
     */
    public function store(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'precisao'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . implode(' | ', $validator->errors()->all()),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $processo = $this->processoAcessivel($id);
        if (!$processo) {
            return response()->json(['success' => false, 'message' => 'Processo não acessível'], 403);
        }

        // Apenas 1 check-in ativo por processo.
        $existente = ProcessoCheckin::where('cd_processo_pro', $processo->cd_processo_pro)->first();
        if ($existente) {
            return response()->json([
                'success'  => false,
                'message'  => 'Check-in já registrado para este processo em '
                              . \Carbon\Carbon::parse($existente->dt_checkin_pck)->format('d/m/Y H:i') . '.',
                'checkin'  => $existente,
            ], 409);
        }

        $latitude   = $request->input('latitude');
        $longitude  = $request->input('longitude');
        $precisao   = $request->input('precisao');
        $endereco   = $request->input('endereco');
        $observacao = $request->input('observacao');
        $dtCheckin  = $request->input('dt_checkin')
            ? date('Y-m-d H:i:s', strtotime($request->input('dt_checkin')))
            : date('Y-m-d H:i:s');

        try {
            $ck = ProcessoCheckin::create([
                'cd_processo_pro'         => $processo->cd_processo_pro,
                'cd_conta_con'            => $processo->cd_conta_con,
                'cd_correspondente_cor'   => $processo->cd_correspondente_cor,
                'cd_entidade_ete'         => Auth::user()->cd_entidade_ete,
                'cd_user_checkin_pck'     => Auth::user()->id,
                'dt_checkin_pck'          => $dtCheckin,
                'nu_latitude_pck'         => $latitude,
                'nu_longitude_pck'        => $longitude,
                'nu_precisao_metros_pck'  => $precisao,
                'ds_endereco_pck'         => $endereco,
                'ds_observacao_pck'       => $observacao,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // 23505 = unique_violation (constraint uq_pck_processo_ativo)
            if ($e->getCode() === '23505') {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in já registrado para este processo.',
                ], 409);
            }
            throw $e;
        }

        return response()->json([
            'success' => true,
            'checkin' => $ck,
        ]);
    }

    /**
     * Remove (soft-delete) um check-in. Após removido, o correspondente
     * pode registrar um novo (a constraint parcial libera).
     */
    public function destroy($id)
    {
        $ck = ProcessoCheckin::where('cd_processo_checkin_pck', $id)->first();
        if (!$ck) {
            return response()->json(['success' => false, 'message' => 'Não encontrado'], 404);
        }
        if (!$this->processoAcessivel($ck->cd_processo_pro)) {
            return response()->json(['success' => false, 'message' => 'Sem permissão'], 403);
        }

        $ck->delete();

        return response()->json(['success' => true]);
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------

    /**
     * Carrega o processo respeitando a regra de visibilidade do usuário.
     * Retorna null caso o usuário não tenha acesso.
     */
    private function processoAcessivel($id)
    {
        $slug = optional(Auth::user()->role()->first())->slug;

        $q = Processo::where('cd_processo_pro', $id);

        if ($slug === 'correspondente') {
            $q->where('cd_correspondente_cor', $this->cdContaCon);
        } else {
            $q->where('cd_conta_con', $this->cdContaCon);
        }

        return $q->first();
    }
}
