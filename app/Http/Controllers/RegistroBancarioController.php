<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\RegistroBancario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegistroBancarioController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function registros($id)
    {  
        dd(RegistroBancario::with('banco')->where('cd_entidade_ete',$id)->get());

        return response()->json(RegstroBancario::with('tipoConta')->with('banco')->where('cd_entidade_ete',$id)->get()); 
      
    }

    public function excluir($id)
    {  
        $registro = RegistroBancario::where('cd_dados_bancarios_dba',$id)->findOrFail($id);
        
        if($registro->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500); 
      
    }

}