<?php

namespace App\Http\Controllers;

use App\Area;
use App\TipoContato;
use App\Http\Requests\TipoContatoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class TipoContatoController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        $tipos = TipoContato::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_tipo_contato_tct')->get();
        
        return view('configuracoes/tipos-de-contato',['tipos' => $tipos]);
    }

    public function show($id)
    {
        
    }

    public function store(Request $request)
    {
        $tipo = new TipoContato();
  
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/tipos-de-contato');

    }

    public function update(Request $request,$id)
    {
        $tipo = TipoContato::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
 
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/tipos-de-contato');
    }

    public function destroy($id)
    {
        $tipo = TipoContato::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($tipo->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}