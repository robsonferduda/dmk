<?php

namespace App\Http\Controllers;

use App\Fone;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class FoneController extends Controller
{

    private $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = 1;
    }

    public function index()
    {

    }

    public function show($id)
    {   
        return response()->json(Fone::findOrFail($id));  
    }

    public function fones($id)
    {   
        return response()->json(Fone::with('tipo')->where('cd_entidade_ete',$id)->orderBy('cd_fone_fon')->get());  
    }

    public function excluir($id)
    {   
        $fone = Fone::where('cd_fone_fon',$id)->findOrFail($id);
        
        if($fone->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);  
    }

    public function editar(Request $request)
    {   
        $fone = Fone::where('cd_fone_fon',$request->id)->findOrFail($request->id);
        $fone->cd_tipo_fone_tfo = $request->tipo;
        $fone->nu_fone_fon = $request->fone;
        
        if($fone->save())
            return Response::json(array('message' => 'Registro editado com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao editar o registro'), 500);  
    }
}