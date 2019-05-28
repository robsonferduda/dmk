<?php

namespace App\Http\Controllers;

use DB;
use App\Fone;
use App\Contato;
use App\Entidade;
use App\Endereco;
use App\TipoContato;
use App\Cliente;
use App\EnderecoEletronico;
use Illuminate\Http\Request;
use App\Http\Requests\ContatoRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class ContatoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
    	$contatos = array();
        $dados = array();
    	$contato = new Contato();

        $tiposContato = TipoContato::where('cd_conta_con', $this->conta)->orderBy('nm_tipo_contato_tct')->get();

    	if(session('inicial')){

    		$inicial = session('inicial');

            $dados = DB::table('contato_cot')
                        ->leftJoin('tipo_contato_tct','tipo_contato_tct.cd_tipo_contato_tct','=','contato_cot.cd_tipo_contato_tct')
                        ->leftJoin('endereco_ede','endereco_ede.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete')
                        ->leftJoin('vi_fone_max_create_entidate_fon', function($join){
                            $join->on('vi_fone_max_create_entidate_fon.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete');
                            $join->whereNull('vi_fone_max_create_entidate_fon.deleted_at');
                        })
                        ->leftJoin('vi_endereco_eletronico_max_create_entidate_ele', function($join){
                            $join->on('vi_endereco_eletronico_max_create_entidate_ele.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete');
                            $join->whereNull('vi_endereco_eletronico_max_create_entidate_ele.deleted_at');
                        })
                        ->leftJoin('cidade_cde','cidade_cde.cd_cidade_cde','=','endereco_ede.cd_cidade_cde')
                        ->where('contato_cot.cd_conta_con',$this->conta)
                        ->where('contato_cot.nm_contato_cot', 'ilike', $inicial.'%')
                        ->whereNull('contato_cot.deleted_at')
                        ->orderBy('contato_cot.nm_contato_cot')
                        ->select('contato_cot.cd_contato_cot','contato_cot.nm_contato_cot','nm_tipo_contato_tct','nm_cidade_cde','nu_fone_fon','dc_endereco_eletronico_ede')
                        ->get();

    	}

    	return view('contato/index',['dados' => $dados,'tiposContato' => $tiposContato]);
    }

    public function filtrar(Request $request){

        Session::put('inicial',NULL);

        $dados = array();

        $tiposContato = TipoContato::where('cd_conta_con', $this->conta)->orderBy('nm_tipo_contato_tct')->get();

        $nomeCliente = '';
        $codCliente = '';

        $dados = DB::table('contato_cot')
                        ->leftJoin('tipo_contato_tct','tipo_contato_tct.cd_tipo_contato_tct','=','contato_cot.cd_tipo_contato_tct')
                        ->leftJoin('endereco_ede','endereco_ede.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete')
                        ->leftJoin('vi_fone_max_create_entidate_fon', function($join){
                            $join->on('vi_fone_max_create_entidate_fon.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete');
                            $join->whereNull('vi_fone_max_create_entidate_fon.deleted_at');
                        })
                        ->leftJoin('vi_endereco_eletronico_max_create_entidate_ele', function($join){
                            $join->on('vi_endereco_eletronico_max_create_entidate_ele.cd_entidade_ete','=','contato_cot.cd_entidade_contato_ete');
                            $join->whereNull('vi_endereco_eletronico_max_create_entidate_ele.deleted_at');
                        })
                        ->leftJoin('cidade_cde','cidade_cde.cd_cidade_cde','=','endereco_ede.cd_cidade_cde');
                        if(!empty($request->cd_cliente_cli)){
                            $dados->leftJoin('cliente_cli','cliente_cli.cd_entidade_ete','=','contato_cot.cd_entidade_ete')
                                 ->where('cd_cliente_cli', $request->cd_cliente_cli);
                            $codCliente = $request->cd_cliente_cli;

                            $cliente = Cliente::where('cd_conta_con',$this->conta)->where('cd_cliente_cli',$request->cd_cliente_cli)->first();

                            if(!empty($cliente->nm_fantasia_cli)){
                                $nomeCliente =  $cliente->nu_cliente_cli.' - '.$cliente->nm_razao_social_cli.' ('.$cliente->nm_fantasia_cli.')';
                            }else{
                                $nomeCliente = $cliente->nu_cliente_cli.' - '.$cliente->nm_razao_social_cli;
                            }
                            
                        }

                        if(!empty($request->cd_tipo_contato_tct))
                            $dados->where('tipo_contato_tct.cd_tipo_contato_tct',$request->cd_tipo_contato_tct);

            $dados   =  $dados->where('contato_cot.cd_conta_con',$this->conta)
                        ->whereNull('contato_cot.deleted_at')
                        ->orderBy('contato_cot.nm_contato_cot')
                        ->select('contato_cot.cd_contato_cot','contato_cot.nm_contato_cot','nm_tipo_contato_tct','nm_cidade_cde','nu_fone_fon','dc_endereco_eletronico_ede')
                        ->get();

        return view('contato/index',['dados' => $dados, 'codCliente' => $codCliente, 'nomeCliente' => $nomeCliente, 'tiposContato' => $tiposContato, 'tipoContato' => $request->cd_tipo_contato_tct]);
    }

    public function buscar($inicial)
    {
    	Session::put('inicial',$inicial); 
       	return redirect('contatos');
    }

    public function detalhes($id)
    {
        $contato = Contato::where('cd_contato_cot',$id)->first();
        return view('contato/detalhes',['contato' => $contato]);
    }

    public function editar($id)
    {
        $contato = Contato::where('cd_contato_cot',$id)->first();
        $tipos = TipoContato::where('cd_conta_con',$this->conta)->get();

        $nome = '';

        if(!empty($contato->entidadeCliente->cliente)){
            if(!empty($contato->entidadeCliente->cliente->nm_fantasia_cli)){
                    $nome =  $contato->entidadeCliente->cliente->nu_cliente_cli.' - '.$contato->entidadeCliente->cliente->nm_razao_social_cli.' ('.$contato->entidadeCliente->cliente->nm_fantasia_cli.')';
            }else{
                    $nome = $contato->entidadeCliente->cliente->nu_cliente_cli.' - '.$contato->entidadeCliente->cliente->nm_razao_social_cli;
            }
        }

        return view('contato/editar',['contato' => $contato, 'tipos' => $tipos, 'nome' => $nome]);
    }

    public function novo(){

        $tipos = TipoContato::where('cd_conta_con',$this->conta)->get();
    	return view('contato/novo',['tipos' => $tipos]);
    }

    public function store(ContatoRequest $request)
    {

        $request->merge(['nu_cep_ede' => ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null]);
        $request->merge(['cd_conta_con' => $this->conta]);

        DB::transaction(function() use ($request){
        

            if(!empty($request->cd_cliente_cli)){
               
                $cliente = Cliente::where('cd_conta_con', $this->conta)->where('cd_cliente_cli',$request->cd_cliente_cli)->select('cd_entidade_ete')->first(); 
            }

            $entidade = new Entidade;
            $entidade->cd_conta_con = $this->conta;
            $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CONTATO;
            $entidade->saveOrFail();
            $cdEntidade = $entidade->cd_entidade_ete;
            
            if($entidade->cd_entidade_ete){

                $c = Contato::create([
                    'cd_conta_con'              => $this->conta, 
                    'cd_entidade_ete'           => (!empty($cliente->cd_entidade_ete)) ? $cliente->cd_entidade_ete : $entidade->cd_entidade_ete,
                    'cd_entidade_contato_ete'   => $entidade->cd_entidade_ete,
                    'cd_tipo_contato_tct'       => $request->cd_tipo_contato_tct,
                    'nm_contato_cot'            => $request->nm_contato_cot,
                    'dc_observacao_cot'         => $request->dc_observacao_cot
                ]);

                if(!empty($request->dc_logradouro_ede)){

                    $request->merge(['cd_entidade_ete' => $entidade->cd_entidade_ete]);

                    $endereco = new Endereco();
                    $endereco->fill($request->all());
                    $endereco->saveOrFail(); 

                    if(!$endereco){
                        DB::rollBack();
                        Flash::error('Erro ao inserir dados');
                        return redirect('contato/novo');
                    }  
                }

                if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                    $fones = json_decode($request->telefones);
                    for($i = 0; $i < count($fones); $i++) {

                        $fone = Fone::create([
                            'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                            'cd_conta_con'              => $this->conta, 
                            'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                            'nu_fone_fon'               => $fones[$i]->numero
                        ]);

                        if(!$fone){
                            DB::rollBack();
                            Flash::error('Erro ao cadastrar telefone');
                            return redirect('contato/novo');
                        } 

                    }
                }

                //Inserção de emails
                if(!empty($request->emails) && count(json_decode($request->emails)) > 0){

                    $emails = json_decode($request->emails);
                    for($i = 0; $i < count($emails); $i++) {

                        $email = EnderecoEletronico::create([
                            'cd_entidade_ete'                 => $entidade->cd_entidade_ete,
                            'cd_conta_con'                    => $this->conta, 
                            'cd_tipo_endereco_eletronico_tee' => $emails[$i]->tipo,
                            'dc_endereco_eletronico_ede'      => $emails[$i]->email
                        ]);

                        if(!$email){
                            DB::rollBack();
                            Flash::error('Erro ao cadastrar email');
                            return redirect('contato/novo');
                        } 

                    }
                }

            }     
        });
        Flash::success('Contato inserido com sucesso');

        session::put('inicial', strtoupper(trim($request->nm_contato_cot)[0]));

        return redirect('contatos');
    }

    public function update(ContatoRequest $request,$id)
    {
        $contato = Contato::where('cd_contato_cot',$id)->first();

        if(!empty($request->cd_cliente_cli)){
               
            $cliente = Cliente::where('cd_conta_con', $this->conta)->where('cd_cliente_cli',$request->cd_cliente_cli)->select('cd_entidade_ete')->first(); 
            $request->merge(['cd_entidade_ete' => $cliente->cd_entidade_ete]);
        }else{
            $request->merge(['cd_entidade_ete' => $contato->cd_entidade_contato_ete]);
        }
        
        $request->merge(['nu_cep_ede' => ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null]);
        $request->merge(['cd_conta_con' => $this->conta]);

        $contato->fill($request->all());

        if($contato->saveOrFail()){

            # =============== Importante ========================
            $request->merge(['cd_entidade_ete' => $contato->cd_entidade_contato_ete]);

            //Atualização de endereço - Exige que pelo menos o logradouro esteja preenchido
            if(!empty($request->dc_logradouro_ede)){

                $endereco = Endereco::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$contato->cd_entidade_contato_ete)->first();

                if($endereco){
                            

                    $endereco->fill($request->all());
                    $endereco->saveOrFail();

                }else{

                    $endereco = new Endereco();
                    $endereco->fill($request->all());
                    $endereco->saveOrFail();
                }
                        
            }

            if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                $fones = json_decode($request->telefones);
                for($i = 0; $i < count($fones); $i++) {

                    $fone = Fone::create([
                        'cd_entidade_ete'           => $contato->cd_entidade_contato_ete,
                        'cd_conta_con'              => $this->conta, 
                        'cd_tipo_fone_tfo'          => $fones[$i]->tipo,
                        'nu_fone_fon'               => $fones[$i]->numero
                    ]);

                }
            }

            //Inserção de emails
            if(!empty($request->emails) && count(json_decode($request->emails)) > 0){

                $emails = json_decode($request->emails);
                for($i = 0; $i < count($emails); $i++) {

                    $email = EnderecoEletronico::create([
                        'cd_entidade_ete'                 => $contato->cd_entidade_contato_ete,
                        'cd_conta_con'                    => $this->conta, 
                        'cd_tipo_endereco_eletronico_tee' => $emails[$i]->tipo,
                        'dc_endereco_eletronico_ede'      => $emails[$i]->email
                    ]);

                }
            }
        }

        Flash::success('Contato atualizado com sucesso');

        if(!empty($request->cd_cliente_cli)){
            return redirect('cliente/contatos/'.$cliente->cd_entidade_ete);
        }else{

            session::put('inicial', strtoupper(trim($request->nm_contato_cot)[0]));

            return redirect('contatos');
        }
    }

    public function destroy($id)
    {

        $contato = Contato::where('cd_contato_cot',$id)->first();

        if($contato->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        
    }
}