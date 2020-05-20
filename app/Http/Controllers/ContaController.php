<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Hash;
use App\Role as RoleSistema;
use App\Utils;
use App\User;
use App\Fone;
use App\Conta;
use App\Banco;
use App\Estado;
use App\Entidade;
use App\TipoConta;
use App\TipoFone;
use App\Endereco;
use App\RegistroBancario;
use App\EnderecoEletronico;
use App\Identificacao;
use App\Traits\BootConta;
use App\Enums\Nivel;
use App\Enums\Roles;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\ContaRequest;
use Kodeine\Acl\Models\Eloquent\Role;
use Illuminate\Support\Facades\Session;

class ContaController extends Controller
{
    use BootConta;
    
    public function __construct()
    {
        //$this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        return view('home');
    }

    public function detalhes($id)
    {
        $id = \Crypt::decrypt($id);

        //Verifica se o usuário logado é o mesmo que requisitou os dados
        if(Auth::user()->cd_conta_con != $id){ return redirect('erro-permissao'); }

        $conta = Conta::with(['entidade' => function($query){
                            $query->where('cd_tipo_entidade_tpe',\TipoEntidade::CONTA);
                            $query->with('usuario');
                            $query->with('endereco');
                        }])
                        ->with('tipoPessoa')
                        ->where('cd_conta_con',$id)
                        ->first();

        return view('conta/detalhes',['conta' => $conta]);

    }

    public function editar($id)
    {
        $id = \Crypt::decrypt($id);
        
        //Verifica se o usuário logado é o mesmo que requisitou os dados
        if(Auth::user()->cd_conta_con != $id){ 
            return redirect('erro-permissao');
        }

        $conta   = Conta::with(['entidade' => function($query){
                            $query->where('cd_tipo_entidade_tpe',\TipoEntidade::CONTA);
                            $query->with('usuario');
                            $query->with('endereco');
                        }])
                        ->with('tipoPessoa')
                        ->where('cd_conta_con',$id)
                        ->first();

        $estados = Estado::orderBy('nm_estado_est')->get();
        $tiposFone = TipoFone::orderBy('dc_tipo_fone_tfo')->get();
        $bancos        = Banco::orderBy('cd_banco_ban')->get();
        $tiposConta    = TipoConta::orderBy('nm_tipo_conta_tcb')->get();

        return view('conta/editar',['conta' => $conta, 'estados' => $estados, 'tiposFone' => $tiposFone, 'bancos' => $bancos, 'tiposConta' => $tiposConta]);

    }

    public function store(ContaRequest $request)
    {

        DB::transaction(function() use ($request){

            $input = $request->all();
            $email = $input['email']; 
            $nome  = $input['nm_razao_social_con'];
            $senha = $input['password'];

        	$conta = new Conta();        
            $conta->fill($request->all());
            $conta->saveOrFail();

            if($conta->cd_conta_con){

                $this->bootConta($conta->cd_conta_con);

                $entidade = new Entidade;
                $entidade->cd_conta_con = $conta->cd_conta_con;
                $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CONTA;
                $entidade->saveOrFail();

                if($entidade->cd_entidade_ete){

                    $user = new User();
                    $user->cd_conta_con = $conta->cd_conta_con;
                    $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                    $user->cd_nivel_niv = Nivel::ADMIN;
                    $user->name = $nome;
                    $user->email = $email;
                    $user->password = Hash::make($senha);
                    $user->save();

                    //Atribuição de roles para o usuário como administrador
                    $role = RoleSistema::find(Roles::ADMINISTRADOR);
                    if($user->assignRole($role)){

                        $perms = $role->permissao()->get();
                        
                        foreach ($perms as $p) {
                            $user->permissao()->attach($p);
                        }

                    }

                    Session::put('SESSION_CD_CONTA', $conta->cd_conta_con); //Grava o id da conta para ser utilizado nos cadastros que exigem 
                    Session::put('SESSION_CD_ENTIDADE', $entidade->cd_entidade_ete); //Grava o id da conta para ser utilizado nos cadastros que exigem 

                    Auth::login($user);
                }
            }
        });
        return redirect('home');
    }

    public function update(Request $request,$id)
    {
        $conta = Conta::where('cd_conta_con',$id)->first();

        $cep = ($request->nu_cep_ede) ? str_replace("-", "", $request->nu_cep_ede) : null;

        $request->merge(['nu_cep_ede' => $cep]);
        $request->merge(['cd_conta_con' => $this->conta]);
        $request->merge(['cd_entidade_ete' => $conta->entidade->cd_entidade_ete]);

        $conta->fill($request->all());

        if($conta->saveOrFail()){

            //Se existe identificação para tipo de pessoa
            if($request->cpf or $request->cnpj){
                
                $identificacao = (Identificacao::where('cd_conta_con',$conta->entidade->cd_conta_con)->where('cd_entidade_ete',$conta->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first()) ? Identificacao::where('cd_conta_con',$conta->entidade->cd_conta_con)->where('cd_entidade_ete',$conta->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CPF)->first() : $identificacao = Identificacao::where('cd_conta_con',$conta->entidade->cd_conta_con)->where('cd_entidade_ete',$conta->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::CNPJ)->first();

                $nu_cpf_cnpj = ($request->cd_tipo_pessoa_tpp == 1) ? Utils::limpaCPF_CNPJ($request->cpf) : Utils::limpaCPF_CNPJ($request->cnpj);
                
                if($identificacao){

                    $identificacao->cd_tipo_identificacao_tpi = ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ;
                    $identificacao->nu_identificacao_ide = (!empty($nu_cpf_cnpj)) ? $nu_cpf_cnpj : '';
                    $identificacao->saveOrFail();

                }else{

                    $identificacao = Identificacao::create([
                        'cd_entidade_ete'           => $conta->entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->conta, 
                        'cd_tipo_identificacao_tpi' => ($request->cd_tipo_pessoa_tpp == 1) ? \TipoIdentificacao::CPF : \TipoIdentificacao::CNPJ,
                        'nu_identificacao_ide'      => (!empty($nu_cpf_cnpj)) ? $nu_cpf_cnpj : ''
                    ]);
                }
            }

            if($request->oab){

                //Registro de OAB
                $identificacao_oab = Identificacao::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$conta->entidade->cd_entidade_ete)->where('cd_tipo_identificacao_tpi',\TipoIdentificacao::OAB)->first();

                if($identificacao_oab){
                        
                    $request->merge(['nu_identificacao_ide' => $request->oab]);
                    $identificacao_oab->fill($request->all());
                    $identificacao_oab->saveOrFail();
                                
                }else{

                    $identificacao_oab = Identificacao::create([
                        'cd_entidade_ete'           => $conta->entidade->cd_entidade_ete,
                        'cd_conta_con'              => $this->conta, 
                        'cd_tipo_identificacao_tpi' => \TipoIdentificacao::OAB,
                        'nu_identificacao_ide'      => $request->oab
                    ]);

                }  
            }

            //Atualização de endereço - Exige que pelo menos o logradouro esteja preenchido
            if(!empty($request->dc_logradouro_ede)){

                $endereco = Endereco::where('cd_conta_con',$this->conta)->where('cd_entidade_ete',$conta->entidade->cd_entidade_ete)->first();

                if($endereco){
                        
                        $endereco->fill($request->all());
                        $endereco->saveOrFail();

                }else{

                        $endereco = new Endereco();
                        $endereco->fill($request->all());
                        $endereco->saveOrFail();
                }
                    
            }      

             //Inserção de telefones
            if(!empty($request->telefones) && count(json_decode($request->telefones)) > 0){

                $fones = json_decode($request->telefones);
                for($i = 0; $i < count($fones); $i++) {

                    $fone = Fone::create([
                        'cd_entidade_ete'           => $conta->entidade->cd_entidade_ete,
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
                        'cd_entidade_ete'                 => $conta->entidade->cd_entidade_ete,
                        'cd_conta_con'                    => $this->conta, 
                        'cd_tipo_endereco_eletronico_tee' => $emails[$i]->tipo,
                        'dc_endereco_eletronico_ede'      => $emails[$i]->email
                    ]);

                }
            } 

            //Dados Bancários
            if(!empty($request->registrosBancarios) && count(json_decode($request->registrosBancarios)) > 0){

                    $registrosBancarios = json_decode($request->registrosBancarios);
                    for($i = 0; $i < count($registrosBancarios); $i++) {

                        $registro = RegistroBancario::create([
                            'cd_entidade_ete' => $conta->entidade->cd_entidade_ete,
                            'cd_conta_con'    => $this->conta, 
                            'nm_titular_dba'  => $registrosBancarios[$i]->titular,
                            'nu_cpf_cnpj_dba' => str_replace(array('.','-'),'',$registrosBancarios[$i]->cpf),
                            'nu_agencia_dba'  => $registrosBancarios[$i]->agencia,
                            'nu_conta_dba'    => $registrosBancarios[$i]->conta,
                            'cd_banco_ban'    => $registrosBancarios[$i]->banco,
                            'cd_tipo_conta_tcb' => $registrosBancarios[$i]->tipo
                        ]);


                    }
            }

            Flash::success('Dados atualizados com sucesso');
        }else{
            Flash::error('Erro ao atualizar dados');
        }

        return redirect('conta/detalhes/'.\Crypt::encrypt($id));
    }

    public function atualizarFlagEnvio(Request $request)
    {
        if($request->flag == 'true') $flag = 'S'; else $flag = 'N';

        $conta = Conta::where('cd_conta_con',$request->conta)->first();
        $conta->fl_envio_enter_con = $flag;

        $conta->save();
    }

    public function prazos()
    {
        $conta = Conta::where('cd_conta_con',$this->conta)->first();
        return view('configuracoes/prazos',['conta' => $conta]);
    }

    public function salvarPrazos(Request $request)
    {
        $conta = Conta::find($this->conta);

        if(isset($request->prazo_cancelamento_processo)){

            $conta->prazo_cancelamento_processo = $request->prazo_cancelamento_processo;
            if($conta->save()){
                Flash::success('Prazos atualizados com sucesso');
            }else{
                Flash::error('Erro ao atualizar prazos');
            }
        }

        return redirect('configuracoes/prazos');
    }

}