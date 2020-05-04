<?php

namespace App\Http\Controllers;

use Auth;
use App\AnexoProcesso;
use App\Enums\TipoAnexoProcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AnexoProcessoController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
        
    }

    public function show($id)
    {   
        $id = \Crypt::decrypt($id);
        $anexo = AnexoProcesso::where('cd_anexo_processo_apr',$id)->first();
        return response()->download(storage_path($anexo->nm_local_anexo_despesa_des.$anexo->nm_anexo_despesa_des));
    }

    public function showPlugin($id, $file)
    {   
        $anexo = AnexoProcesso::where('cd_processo_pro', $id)->where('nm_anexo_processo_apr','ilike',$file)->first();
        return response()->download(storage_path($anexo->nm_local_anexo_processo_apr.$anexo->nm_anexo_processo_apr));
    }

    public function create(Request $request)
    {

        $local = "processos/$request->id_processo/";
        $tipo = (Auth::user()->cd_nivel_niv == 3) ? TipoAnexoProcesso::CORRESPONDENTE : TipoAnexoProcesso::CONTA;

        AnexoProcesso::create([
            'cd_conta_con'                => $this->conta, 
            'cd_entidade_ete'             => $this->entidade,
            'cd_tipo_anexo_processo_tap'  => $tipo,
            'cd_processo_pro'             => $request->id_processo,
            'nm_anexo_processo_apr'       => $request->nome_arquivo,
            'nm_local_anexo_processo_apr' => $local     
        ]);
    }

    public function destroy(Request $request)
    {

        $anexo = AnexoProcesso::where('cd_processo_pro',$request->id)->where('nm_anexo_processo_apr',$request->nome_arquivo)->first();

        if($anexo->delete()){

            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        
        }else{
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }
        
    }

    public function destroyAndRemoveFile($id)
    {
 
        
    }

}