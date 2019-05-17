<?php

namespace App\Http\Controllers;

use App\Vara;
use App\Http\Requests\VaraRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;
use Excel;
use App\Imports\VaraImport;

class VaraController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        $sub = \DB::table('vara_var')->selectRaw("cd_vara_var , regexp_replace(substring(nm_vara_var from 0 for 4), '\D', '', 'g') as number , concat(REGEXP_REPLACE(substring(nm_vara_var from 0 for 4), '[[:digit:]]' ,'','g'),  substring(nm_vara_var from 4))  as caracter ")->whereNull('deleted_at')->toSql();

        $varas = \DB::table(\DB::raw("($sub) as sub "))
        ->selectRaw("cd_vara_var, concat(number,caracter) as nm_vara_var")
        ->orderByRaw("nullif(number,'')::int,caracter")
        ->get();
        
        return view('configuracoes/varas',['varas' => $varas]);
    }

    public function show($id)
    {
        $vara = Vara::findOrFail($id);     
        return response()->json($vara);  
    }

    public function store(VaraRequest $request)
    {
        $vara = new Vara();
 
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/varas');

    }

    public function update(VaraRequest $request,$id)
    {
        $vara = Vara::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/varas');
    }

    public function destroy($id)
    {
        $vara = Vara::where('cd_conta_con',$this->cdContaCon)->where('cd_vara_var',$id)->first();
        
        if($vara->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }

    public function importar(Request $request){
        
        $file = $request->file('file');

        $extensions = array("xls","xlsx","XLSX","XLS");

        if($file){
            
            $path = $file->getRealPath();

            if(in_array($file ->getClientOriginalExtension(),$extensions)){
                
                try {
                    $data =  Excel::import(new VaraImport,$file);
                } catch (\ErrorException $e) {
                    Flash::error('Erro ao atualizar dados. Msg: '.$e->getMessage());
                }
            }else{
                Flash::error('Erro ao atualizar dados');
            }
        }else{
            Flash::error('Erro ao atualizar dados');
        }

        return redirect('configuracoes/varas');
    }
}