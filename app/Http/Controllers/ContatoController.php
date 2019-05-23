<?php

namespace App\Http\Controllers;

use DB;
use App\Fone;
use App\Contato;
use App\Entidade;
use App\Endereco;
use App\TipoContato;
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

    	if(session('inicial')){

    		$inicial = session('inicial');

            $dados = DB::table('contato_cot')
                        ->leftJoin('tipo_contato_tct','tipo_contato_tct.cd_tipo_contato_tct','=','contato_cot.cd_tipo_contato_tct')
                        ->leftJoin('entidade_ete','entidade_ete.cd_entidade_ete','=','contato_cot.cd_entidade_ete')
                        ->leftJoin('endereco_ede','endereco_ede.cd_entidade_ete','=','entidade_ete.cd_entidade_ete')
                        ->leftJoin('fone_fon','fone_fon.cd_entidade_ete','=','entidade_ete.cd_entidade_ete')
                        ->leftJoin('endereco_eletronico_ele','endereco_eletronico_ele.cd_entidade_ete','=','entidade_ete.cd_entidade_ete')
                        ->leftJoin('cidade_cde','cidade_cde.cd_cidade_cde','=','endereco_ede.cd_cidade_cde')
                        ->where('contato_cot.cd_conta_con',$this->conta)
                        ->where('contato_cot.nm_contato_cot', 'ilike', $inicial.'%')
                        ->whereNull('contato_cot.deleted_at')
                        ->select('contato_cot.cd_contato_cot','contato_cot.nm_contato_cot','nm_tipo_contato_tct','nm_cidade_cde','nu_fone_fon','dc_endereco_eletronico_ede')
                        ->get();

    	}

    	return view('contato/index',['dados' => $dados]);
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

        return view('contato/editar',['contato' => $contato, 'tipos' => $tipos]);
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
        
            $entidade = new Entidade;
            $entidade->cd_conta_con = $this->conta;
            $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CONTATO;
            $entidade->saveOrFail();

            if($entidade->cd_entidade_ete){

                $c = Contato::create([
                    'cd_conta_con'              => $this->conta, 
                    'cd_entidade_ete'           => $entidade->cd_entidade_ete,
                    'cd_entidade_contato_ete'   => $entidade->cd_entidade_ete,
                    'cd_tipo_contato_tct'       => $request->cd_tipo_contato_tct,
                    'nm_contato_cot'            => $request->nm_contato_cot
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
        return redirect('contatos');
    }

    public function update(ContatoRequest $request,$id)
    {

        $request->merge(['nu_cep_ede' => ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null]);
        $request->merge(['cd_conta_con' => $this->conta]);

        $contato = Contato::where('cd_contato_cot',$id)->first();
        $contato->fill($request->all());

        if($contato->saveOrFail()){

            //Atualização de endereço - Exige que pelo menos o logradouro esteja preenchido
            if(!empty($request->dc_logradouro_ede)){

                $endereco = Endereco::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$contato->cd_entidade_ete)->first();

                if($endereco){
                            
                    $endereco->fill($request->all());
                    $endereco->saveOrFail();

                }else{

                    $endereco = new Endereco();
                    $endereco->fill($request->all());
                    $endereco->saveOrFail();
                }
                        
            }
        }

        Flash::success('Contato atualizado com sucesso');
        return redirect('contatos');
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