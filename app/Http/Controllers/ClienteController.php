<?php

namespace App\Http\Controllers;

use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClienteController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function novo()
    {
        return view('cliente/novo');
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function listar()
    {
        $clientes = Cliente::take(10)->orderBy('created_at','ASC')->get();
        return view('cliente/clientes',['clientes' => $clientes]);
    }

    public function buscar(Request $request)
    {
        $nome = $request->get('nome');
        $identificacao = $request->get('identificacao');
        $tipo = $request->get('tipo_pessoa');
        $situacao = $request->get('situacao');

        $clientes = Cliente::join('entidade_ete', function($join) use($nome, $tipo, $situacao){

                                $join->on('cliente_cli.cd_entidade_ete','=','entidade_ete.cd_entidade_ete');
                                $join->where('entidade_ete.cd_tipo_entidade_tpe','=',8);
                                if(!empty($nome)) $join->where('nm_razao_social_cli','like','%'.$nome.'%');
                                if(!empty($tipo)) $join->where('cliente_cli.cd_tipo_pessoa_tpp','=',$tipo);
                                if(!empty($situacao)) $join->where('fl_ativo_cli','=',$situacao);

                            })->leftJoin('identificacao_ide', function($join) use ($identificacao){
                                $join->on('cliente_cli.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                if(!empty($identificacao)) $join->where('nu_identificacao_ide','=',$identificacao);
                            })->get();

        return view('cliente/clientes',['clientes' => $clientes]);
    }

}