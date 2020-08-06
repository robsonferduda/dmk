<?php

namespace App\Http\Controllers;

use App\EnderecoEletronico;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class EnderecoEletronicoController extends Controller
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
        return response()->json(EnderecoEletronico::findOrFail($id));  
    }

    public function email($id)
    {   
        return response()->json(EnderecoEletronico::with('tipo')->where('cd_entidade_ete',$id)->get());  
    }

    public function excluir($id)
    {   
        $email = EnderecoEletronico::where('cd_endereco_eletronico_ele',$id)->findOrFail($id);
        
        if($email->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);  
    }

    public function editar(Request $request)
    {   
        $email = EnderecoEletronico::where('cd_endereco_eletronico_ele',$request->id)->findOrFail($request->id);
        $email->cd_tipo_endereco_eletronico_tee = $request->tipo;
        $email->dc_endereco_eletronico_ede = $request->email;
        
        if($email->save())
            return Response::json(array('message' => 'Registro editado com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao editar o registro'), 500);  
    }
}