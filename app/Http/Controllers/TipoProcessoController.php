<?php

namespace App\Http\Controllers;

use App\Area;
use App\TipoProcesso;
use App\Http\Requests\TipoProcessoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class TipoProcessoController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        $tipos = TipoProcesso::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_tipo_processo_tpo')->get();
        
        return view('configuracoes/tipos-de-processo',['tipos' => $tipos]);
    }

    public function show($id)
    {
        
    }

    public function store(TipoProcessoRequest $request)
    {

        \Cache::tags($this->cdContaCon,'listaTiposProcesso')->flush();

        $tipo = new TipoProcesso();
  
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/tipos-de-processo');

    }

    public function update(Request $request,$id)
    {

        \Cache::tags($this->cdContaCon,'listaTiposProcesso')->flush();

        $tipo = TipoProcesso::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        if(isset($request->fl_honorario_tse)){
            $request->merge(['fl_honorario_tse' => 'S']);
        }else{
            $request->merge(['fl_honorario_tse' => 'N']);
        }

        if(isset($request->fl_despesa_tse)){
            $request->merge(['fl_despesa_tse' => 'S']);
        }else{
            $request->merge(['fl_despesa_tse' => 'N']);
        }
        
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/tipos-de-processo');
    }

    public function destroy($id)
    {

        \Cache::tags($this->cdContaCon,'listaTiposProcesso')->flush();

        $tipo = TipoProcesso::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($tipo->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}