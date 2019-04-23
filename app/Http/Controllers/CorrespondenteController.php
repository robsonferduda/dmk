<?php

namespace App\Http\Controllers;

use App\Correspondente;
use App\ContaCorrespondente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class CorrespondenteController extends Controller
{

    public $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        $correspondente = new ContaCorrespondente();
        $correspondentes = $correspondente::where('cd_conta_con',$this->conta)->get();

        return view('correspondente/correspondentes',['correspondetes' => $correspondentes]);
    }

    public function buscar(Request $request){

        $estado = $request->get('cd_estado_est');
        $cidade = $request->get('cd_cidade_cde');
        $nome = $request->get('nome');
        $identificacao = $request->get('identificacao');

        $correspondentes = ContaCorrespondente::join('conta_con', function($join) use($estado, $cidade, $nome, $identificacao){
                                            
                                $join->on('conta_correspondente_ccr.cd_correspondente_cor','=','conta_con.cd_conta_con');
                                if(!empty($nome)) $join->where('nm_fantasia_con','like','%'.$nome.'%');

                                $join->join('entidade_ete', function($join) use ($identificacao){
                                    $join->on('conta_con.cd_conta_con','=','entidade_ete.cd_conta_con');

                                    if(!empty($identificacao)){
                                        $join->join('identificacao_ide', function($join) use ($identificacao){
                                            $join->on('entidade_ete.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                            $join->where('nu_identificacao_ide','=',$identificacao);
                                        });
                                    }
                                });

                                if(!empty($cidade)){
                                    $join->join('taxa_honorario_entidade_the', function($join) use ($cidade){
                                        $join->on('entidade_ete.cd_entidade_ete','=','taxa_honorario_entidade_the.cd_entidade_ete');
                                        $join->where('cd_cidade_cde','=',$cidade);
                                    });
                                }
                                
                            })
                            ->orderBy('conta_con.nm_fantasia_con','DESC')
                            ->get();

        return view('correspondente/correspondentes',['correspondetes' => $correspondentes]);

    }

    public function buscarTodos(Request $request){

        $nome = $request->get('nome');
        $identificacao = $request->get('identificacao');

        $correspondentes = Correspondente::join('entidade_ete', function($join) use ($identificacao){
                                    
                                    $join->on('conta_con.cd_conta_con','=','entidade_ete.cd_conta_con');
                                    $join->where('cd_tipo_entidade_tpe',\TipoEntidade::CORRESPONDENTE);
                                    if(!empty($nome)) $join->where('nm_fantasia_con','like','%'.$nome.'%');
                                    
                                    if(!empty($identificacao)){
                                        $join->join('identificacao_ide', function($join) use ($identificacao){
                                            $join->on('entidade_ete.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                            $join->where('nu_identificacao_ide','=',$identificacao);
                                        });
                                    }                            
                            })
                            ->orderBy('conta_con.nm_fantasia_con','DESC')
                            ->get();

        if(count($correspondentes) == 0)
            \Session::put('busca_vazia', true);

        return view('correspondente/novo',['correspondetes' => $correspondentes]);

    }

    public function novo(){
        return view('correspondente/novo');
    }

}