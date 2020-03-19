<?php

namespace App\Http\Controllers;

use App\AnexoDespesa;
use App\Http\Requests\CidadeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AnexoDespesaController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        
    }

    public function show($id)
    {   
        $id = \Crypt::decrypt($id);
        $anexo = AnexoDespesa::where('cd_anexo_despesa_des',$id)->first();
        return response()->download(storage_path($anexo->nm_local_anexo_despesa_des.$anexo->nm_anexo_despesa_des));
    }

    public function create(Request $request)
    {

        $local = "despesas/$this->conta/$request->id_despesa/";

        AnexoDespesa::create([
            'cd_conta_con'   => $this->conta,
            'cd_despesa_des' => $request->id_despesa,
            'nm_anexo_despesa_des' => $request->nome_arquivo,
            'nm_local_anexo_despesa_des' => $local
        ]);
    }

    public function destroy(Request $request)
    {

        $anexo = AnexoDespesa::where('cd_despesa_des',$request->id)->where('nm_anexo_despesa_des',$request->nome_arquivo)->first();

        if($anexo->delete()){

            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        
        }else{
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }
        
    }

     public function destroyAndRemoveFile($id)
    {

        $anexo = AnexoDespesa::where('cd_anexo_despesa_des',$id)->first();

        if($anexo->delete()){

            //Após excluir o registro, exclui o arquivo também
            if(file_exists(storage_path($anexo->nm_local_anexo_processo_apr)))
                unlink(storage_path($anexo->nm_local_anexo_processo_apr));

            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        
        }else{
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        }
        
    }

}