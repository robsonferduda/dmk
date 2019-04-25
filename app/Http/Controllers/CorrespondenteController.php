<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Mail;
use Hash;
use App\Enums\Nivel;
use App\User;
use App\Entidade;
use App\Correspondente;
use App\ContaCorrespondente;
use Illuminate\Http\Request;
use App\Http\Requests\ConviteRequest;
use App\Http\Requests\CorrespondenteRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class CorrespondenteController extends Controller
{

    public $conta;

    public function __construct()
    {
        $this->middleware('auth',['except' => ['cadastro']]);
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        $correspondente = new ContaCorrespondente();
        $correspondentes = $correspondente::where('cd_conta_con',$this->conta)->get();

        return view('correspondente/correspondentes',['correspondetes' => $correspondentes]);
    }

    public function painel()
    {
        return view('correspondente/painel');
    }

    public function buscar(Request $request){

        $estado = $request->get('cd_estado_est');
        $cidade = $request->get('cd_cidade_cde');
        $nome = $request->get('nome');
        $identificacao = $request->get('identificacao');

        $correspondentes = ContaCorrespondente::join('conta_con', function($join) use($estado, $cidade, $nome, $identificacao){
                                            
                                $join->on('conta_correspondente_ccr.cd_correspondente_cor','=','conta_con.cd_conta_con');
                                if(!empty($nome)) $join->where('nm_razao_social_con','like','%'.$nome.'%');

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
                            ->orderBy('conta_con.nm_razao_social_con','DESC')
                            ->get();

        return view('correspondente/correspondentes',['correspondetes' => $correspondentes]);

    }

    public function buscarTodos(Request $request){

        $nome = $request->get('nome');
        $email = $request->get('email');
        $identificacao = $request->get('identificacao');
        $correspondentes = array();

        if(empty($nome) and empty($email) and empty($identificacao)){
            Flash::warning('Preencha pelo menos um termo para a busca');
            return view('correspondente/novo',['correspondetes' => $correspondentes]);
        }

        $correspondentes = Correspondente::join('entidade_ete', function($join) use ($nome, $email, $identificacao){
                                    
                                    $join->on('conta_con.cd_conta_con','=','entidade_ete.cd_conta_con');
                                    $join->where('cd_tipo_entidade_tpe',\TipoEntidade::CORRESPONDENTE);
                                    if(!empty($nome)) $join->where('nm_razao_social_con','like','%'.$nome.'%');
                                    
                                    if(!empty($identificacao)){
                                        $join->join('identificacao_ide', function($join) use ($identificacao){
                                            $join->on('entidade_ete.cd_entidade_ete','=','identificacao_ide.cd_entidade_ete');
                                            $join->where('nu_identificacao_ide','=',$identificacao);
                                        });
                                    } 

                                    if(!empty($email)){
                                        $join->join('users', function($join) use ($email){
                                            $join->on('entidade_ete.cd_entidade_ete','=','users.cd_entidade_ete');
                                            $join->where('email','=',$email);
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

    public function convidar(ConviteRequest $request){

        $to_name = 'Convidado';
        $to_email = $request->email;
        
        $data = array('nome'=>Auth::user()->name);
            
        Mail::send('correspondente/email_convite', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                    ->subject('Cadastro Sistema DMK');
            $message->from('robsonferduda@gmail.com','Administrador do Sistema');
        });

        if(Mail::failures()){


        }

        return redirect('correspondente/novo');

    }

    public function cadastro(CorrespondenteRequest $request){

        DB::transaction(function() use ($request){

            $input = $request->all();
            $email = $input['email']; 
            $nome  = $input['nm_razao_social_con'];
            $senha = $input['password'];

            $conta = new Correspondente();        
            $conta->fill($request->all());
            $conta->fl_correspondente_con = "S";
            $conta->saveOrFail();

            if($conta->cd_conta_con){

                $entidade = new Entidade;
                $entidade->cd_conta_con = $conta->cd_conta_con;
                $entidade->cd_tipo_entidade_tpe = \TipoEntidade::CORRESPONDENTE;
                $entidade->saveOrFail();

                if($entidade->cd_entidade_ete){

                    $user = new User();
                    $user->cd_conta_con = $conta->cd_conta_con;
                    $user->cd_entidade_ete = $entidade->cd_entidade_ete;
                    $user->cd_nivel_niv = Nivel::CORRESPONDENTE;
                    $user->name = $nome;
                    $user->email = $email;
                    $user->password = Hash::make($senha);
                    $user->save();

                    Auth::login($user);
                }
            }
        });
        return redirect('home');
    }

    public function adicionar(Request $request){

        $correspondente = ContaCorrespondente::where('cd_correspondente_cor',$request->id)->first();

        if(is_null($correspondente)){

            $correspondente = new ContaCorrespondente();
            $correspondente->cd_conta_con = $this->conta;
            $correspondente->cd_correspondente_cor = $request->id;        
            
            if($correspondente->save())
                Flash::success('Correspondente adicionado com sucesso');
            else{
                Flash::error('Erro ao adicionar correspondente');
                return redirect()->back();
            }

        }else{
            Flash::warning('Correspondente já existe na sua lista');
            return redirect()->back();
        }

        return redirect('correspondentes');
    }

}