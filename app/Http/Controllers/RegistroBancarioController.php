<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\RegistroBancario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class RegistroBancarioController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function registros($id)
    {  

        $registro = DB::table('dados_bancarios_dba')
                ->join('banco_ban','dados_bancarios_dba.cd_banco_ban','=','banco_ban.cd_banco_ban')
                ->join('tipo_conta_banco_tcb','dados_bancarios_dba.cd_tipo_conta_tcb','=','tipo_conta_banco_tcb.cd_tipo_conta_tcb')
                ->where('dados_bancarios_dba.cd_entidade_ete','=',$id)
                ->whereNull('dados_bancarios_dba.deleted_at')
                ->selectRaw("concat(banco_ban.cd_banco_ban,' - ',banco_ban.nm_banco_ban) as nm_banco_ban, banco_ban.cd_banco_ban, dados_bancarios_dba.*,tipo_conta_banco_tcb.*")
                ->get();

        return response()->json($registro); 
      
    }

    public function registro($id)
    {  

        $registro = DB::table('dados_bancarios_dba')
                ->join('banco_ban','dados_bancarios_dba.cd_banco_ban','=','banco_ban.cd_banco_ban')
                ->join('tipo_conta_banco_tcb','dados_bancarios_dba.cd_tipo_conta_tcb','=','tipo_conta_banco_tcb.cd_tipo_conta_tcb')
                ->where('dados_bancarios_dba.cd_dados_bancarios_dba','=',$id)
                ->whereNull('dados_bancarios_dba.deleted_at')
                ->get();

        return response()->json($registro); 
      
    }

    public function excluir($id)
    {  
        $registro = RegistroBancario::where('cd_dados_bancarios_dba',$id)->findOrFail($id);
        
        if($registro->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500); 
      
    }

     public function editar(Request $request)
    {   
        $rb = RegistroBancario::where('cd_dados_bancarios_dba',$request->id)->findOrFail($request->id);
        
        if($rb->save())
            return Response::json(array('message' => 'Registro editado com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao editar o registro'), 500);  
    }

}