<?php

namespace App\Http\Controllers;

use App\Fone;
use App\Cliente;
use App\Endereco;
use App\Entidade;
use App\Identificacao;
use App\ReembolsoTipoDespesa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\ClienteRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class ClienteController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clientes = Cliente::with('tipoPessoa')->take(10)->orderBy('created_at','ASC')->get();
        return view('cliente/clientes',['clientes' => $clientes]);
    }

    public function detalhes($id)
    {
        $cliente = Cliente::with('entidade')->where('cd_cliente_cli',$id)->first();
        return view('cliente/detalhes',['cliente' => $cliente]);
    }

    public function honorarios()
    {
        $clientes = Cliente::take(10)->orderBy('created_at','ASC')->get();
        return view('cliente/clientes',['clientes' => $clientes]);
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function novo()
    {
        return view('cliente/novo');
    }

    //Chamada para a tela de novo cliente. Carrega os Modelos necessários na view
    public function listar()
    {
        $clientes = Cliente::take(10)->orderBy('created_at','DESC')->get();
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
                                if(!empty($nome)) $join->where('nm_fantasia_cli','like','%'.$nome.'%');
                                if(!empty($tipo)) $join->where('cliente_cli.cd_tipo_pessoa_tpp','=',$tipo);
                                if(!empty($situacao)) $join->where('fl_ativo_cli','=',$situacao);

                            })->leftJoin('identificacao_ide', function($join) use ($identificacao){
                                $join->on('cliente_cli.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                if(!empty($identificacao)) $join->where('nu_identificacao_ide','=',$identificacao);
                            })
                            ->orderBy('cliente_cli.created_at','DESC')
                            ->get();

        return view('cliente/clientes',['clientes' => $clientes]);
    }

    public function store(ClienteRequest $request)
    {

        $cd_conta_con = \Session::get('SESSION_CD_CONTA');

        $request->merge(['nu_cep_ede' => str_replace("-", "", $request->nu_cep_ede)]);
        $request->merge(['cd_conta_con' => $cd_conta_con]);

        $entidade = Entidade::create([
            'cd_conta_con'         => \Session::get('SESSION_CD_CONTA'),
            'cd_tipo_entidade_tpe' => \TipoEntidade::CLIENTE
        ]);

        $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

        $cliente = new Cliente();
        $cliente->fill($request->all());

        if($cliente->saveOrFail()){

            $nu_identificacao_ide = ($request->cd_tipo_pessoa_tpp == 1) ? $request->cpf : $request->cnpj;

            $identificacao = Identificacao::create([
                'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                'cd_conta_con'              => $cd_conta_con, 
                'cd_tipo_identificacao_tpi' => ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ,
                'nu_identificacao_ide'      => (!empty($nu_identificacao_ide)) ? $nu_identificacao_ide : ''
            ]);

            $endereco = new Endereco();
            $endereco->fill($request->all());
            $endereco->saveOrFail();

            if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                $fones = json_decode($request->telefones);
                for($i = 0; $i < count($fones); $i++) {

                    $fone = Fone::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $cd_conta_con, 
                        'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                        'nu_fone_fon'               => $fones[$i]->numero
                    ]);

                }
            }

            if(!empty($request->despesas)){

                $despesas = $request->despesas;
                for($i = 0; $i < count($despesas); $i++) {

                    $reembolso = ReembolsoTipoDespesa::create([
                        'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                        'cd_conta_con'              => $cd_conta_con, 
                        'cd_tipo_despesa_tds'       => $despesas[$i]
                    ]);
                }

            }

            Flash::success('Dados inseridos com sucesso');
            return redirect('cliente/detalhes/'.$cliente->cd_cliente_cli);
        }
        else{
            Flash::error('Erro ao atualizar dados');
            return redirect('clientes');
        }

    }

    public function editar($id){

        $cliente = Cliente::where('cd_cliente_cli',$id)->first();
        return view('cliente/editar',['cliente' => $cliente]);

    }

    public function update(ClienteRequest $request, $id)
    {
        $cliente = Cliente::where('cd_cliente_cli',$id)->first();
        $cliente->fill($request->all());
        
        if($cliente->saveOrFail()){

            $identificacao = (Identificacao::where('cd_conta_con',$cliente->cd_conta_con)->where('cd_entidade_ete',$cliente->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first()) ? Identificacao::where('cd_conta_con',$cliente->cd_conta_con)->where('cd_entidade_ete',$cliente->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first() : $identificacao = Identificacao::where('cd_conta_con',$cliente->cd_conta_con)->where('cd_entidade_ete',$cliente->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CNPJ)->first();

            $nu_identificacao_ide = ($request->cd_tipo_pessoa_tpp == 1) ? $request->cpf : $request->cnpj;

            if($identificacao){

                $identificacao->cd_tipo_identificacao_tpi = ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ;
                $identificacao->nu_identificacao_ide = (!empty($nu_identificacao_ide)) ? $nu_identificacao_ide : '';
                $identificacao->saveOrFail();

            }else{

                $identificacao = Identificacao::create([
                'cd_entidade_ete'           => $cliente->entidade->cd_entidade_ete,
                'cd_conta_con'              => $cliente->cd_conta_con, 
                'cd_tipo_identificacao_tpi' => ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ,
                'nu_identificacao_ide'      => (!empty($nu_identificacao_ide)) ? $nu_identificacao_ide : ''
                ]);
            }            

            Flash::success('Dados atualizados com sucesso');
        }
        else
            Flash::error('Erro ao atualizar dados');

        return redirect('cliente/detalhes/'.$cliente->cd_cliente_cli);
    }

    public function destroy($id)
    {

        $cliente = Cliente::where('cd_cliente_cli',$id)->first();

        if($cliente->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        
    }

    public function search(Request $request)
    {
        $search = $request->get('term');
      
        $resultados = Cliente::where('nm_razao_social_cli', 'LIKE', '%'. $search. '%')->orWhere('nm_fantasia_cli', 'LIKE', '%'. $search. '%')->get();

        $results = array();
        foreach ($resultados as $ret)
        {

            if(!empty($ret->nm_fantasia_cli)){
                $nome =  $ret->nm_razao_social_cli.' ('.$ret->nm_fantasia_cli.')';
            }else{
                $nome = $ret->nm_razao_social_cli;
            }
            
           $results[] = [ 'id' => $ret->cd_cliente_cli, 'value' => $nome ];
        }
 
        return response()->json($results);
            
    } 

}