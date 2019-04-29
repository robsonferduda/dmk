<?php

namespace App\Http\Controllers;

use Auth;
use App\AreaDireito;
use App\User;
use App\Conta;
use App\Cliente;
use App\Entidade;
use App\TipoPessoa;
use App\TipoEntidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EntidadeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {  

        //Cria a conta proprietária do cleinte
        $conta = new Conta();
        $conta->cd_tipo_pessoa_tpp = 1;
        $conta->nm_razao_social_con = "Robson Fernando Duda";
        $conta->nm_fantasia_con = "Duda Advogados";
        $conta->dc_observacao_con = "Escritório muito bom";

        $entidade = new Entidade();
        $entidade->cd_conta_con = 1;
        $entidade->cd_tipo_entidade_tpe = 5;
        
        //Fim da criação da conta proprietária

        //Cria o cliente, associando a conta criada como dona do registro
        $cliente = new Cliente();
        $cliente->cd_conta_con = 1;
        $cliente->cd_entidade_ete = 75;
        $cliente->cd_tipo_pessoa_tpp = 1;
        $cliente->nu_cliente_cli = 1;
        $cliente->nm_fantasia_cli = "";
        $cliente->nm_razao_social_cli = "Cliente ".rand(10, 50);
        $cliente->fl_ativo_cli = "S";
        //$cliente->save();


        $area = new AreaDireito();
        $area->dc_area_direito_ado = "Criminal";
        

        //dd(Cliente::all());

        $cliente = Cliente::where('cd_cliente_cli',36)->first();


        $cliente->delete();
    }

}