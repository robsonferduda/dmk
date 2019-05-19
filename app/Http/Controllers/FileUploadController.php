<?php
 
namespace App\Http\Controllers;
 
use App\AnexoProcesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
 
class FileUploadController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
        $this->entidade = \Session::get('SESSION_CD_ENTIDADE');
    }
    
    public function upload(Request $request)
    {
        $request->validate(['file' => 'required',]);

        $destino = "processos/$request->id_processo/";
 
        $fileName = time().'.'.request()->file->getClientOriginalExtension();

        if(!is_dir($destino)){
            @mkdir(storage_path($destino), 0775);
        }
 
        if(request()->file->move(storage_path($destino), $fileName)){

            $anexo = AnexoProcesso::create([
                'cd_conta_con'                => $this->conta, 
                'cd_entidade_ete'             => $this->entidade,
                'cd_processo_pro'             => $request->id_processo,
                'nm_anexo_processo_apr'       => $request->arquivo,
                'nm_local_anexo_processo_apr' => $destino.$fileName     
            ]);

            return response()->json(['success'=>'Arquivo enviado com sucesso']);

        }else{
            return Response::json(array('message' => 'Erro ao inserir arquivo'), 500);
        }      
 
    }

    public function show($id)
    {   
        $anexo = AnexoProcesso::where('cd_anexo_processo_apr',$id)->first();
        return response()->download(storage_path($anexo->nm_local_anexo_processo_apr));
    }

    public function destroy($id)
    {

        $anexo = AnexoProcesso::where('cd_anexo_processo_apr',$id)->first();

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