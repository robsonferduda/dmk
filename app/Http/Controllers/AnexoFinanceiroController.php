<?php

namespace App\Http\Controllers;

use App\AnexoFinanceiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AnexoFinanceiroController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        
    }

    public function show($id, $file)
    {   
        $anexo = AnexoFinanceiro::where('cd_processo_taxa_honorario_pth', $id)->where('nm_anexo_financeiro_afn',$file)->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::ENTRADA)->where('cd_conta_con',$this->conta)->first();
        return response()->download(storage_path($anexo->nm_local_anexo_financeiro_afn.$anexo->nm_anexo_financeiro_afn));
    }

    public function create(Request $request)
    {

        $ids = json_decode($request->id_processo_baixa);

        foreach ($ids as $id) {

            $local = "entradas/anexos/$this->conta/$id/";    

            AnexoFinanceiro::create([
                'cd_conta_con'   => $this->conta,
                'cd_processo_taxa_honorario_pth' => $id,
                'cd_tipo_financeiro_tfn' => \TipoFinanceiro::ENTRADA,
                'nm_anexo_financeiro_afn' => $request->nome_arquivo,
                'nm_local_anexo_financeiro_afn' => $local
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $ids = json_decode($request->id);

        foreach ($ids as $id) {

            $controle = true;
            $anexo = AnexoFinanceiro::where('cd_processo_taxa_honorario_pth', $id)->where('nm_anexo_financeiro_afn',$request->nome_arquivo)->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::ENTRADA)->where('cd_conta_con',$this->conta)->first();

            if(!$anexo->delete()){

                $controle = false;
                
            }
        }

        if($controle){
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);   
        }else{
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }

        
    }


    public function showSaida($id, $file)
    {   
        $anexo = AnexoFinanceiro::where('cd_processo_taxa_honorario_pth', $id)->where('nm_anexo_financeiro_afn',$file)->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::SAIDA)->where('cd_conta_con',$this->conta)->first();
        return response()->download(storage_path($anexo->nm_local_anexo_financeiro_afn.$anexo->nm_anexo_financeiro_afn));
    }

    public function createSaida(Request $request)
    {

        $ids = json_decode($request->id_processo_baixa);

        foreach ($ids as $id) {

            $local = "saidas/anexos/$this->conta/$request->id_processo_baixa/";    

            AnexoFinanceiro::create([
                'cd_conta_con'   => $this->conta,
                'cd_processo_taxa_honorario_pth' => $id,
                'cd_tipo_financeiro_tfn' => \TipoFinanceiro::SAIDA,
                'nm_anexo_financeiro_afn' => $request->nome_arquivo,
                'nm_local_anexo_financeiro_afn' => $local
            ]);

        }
    }

    public function destroySaida(Request $request)
    {

        $ids = json_decode($request->id);

        foreach ($ids as $id) {

            $controle = true;
            $anexo = AnexoFinanceiro::where('cd_processo_taxa_honorario_pth', $id)->where('nm_anexo_financeiro_afn',$request->nome_arquivo)->where('cd_tipo_financeiro_tfn',\TipoFinanceiro::SAIDA)->where('cd_conta_con',$this->conta)->first();

            if(!$anexo->delete()){

                $controle = false;
                
            }
        }

        if($controle){
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);   
        }else{
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }
        
    }

}