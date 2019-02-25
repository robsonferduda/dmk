<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaDireito;
use App\Http\Requests\AreaDireitoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class AreaController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = 1;
    }

    public function index()
    {

        $areas = AreaDireito::where('cd_conta_con', $this->cdContaCon)->orderBy('dc_area_direito_ado')->get();
        return view('configuracoes/areas',['areas' => $areas]);
        
    }

    public function show($id)
    {
        
        $area = AreaDireito::findOrFail($id);     
        return response()->json($area);  
    }

    public function store(AreaDireitoRequest $request)
    {

        $area = new AreaDireito();

        $request->merge(['cd_conta_con' => $this->cdContaCon]);
        
        $area->fill($request->all());

        if($area->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/areas');

    }

    public function update(Request $request,$id)
    {
        $area = AreaDireito::findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $area->fill($request->all());

        if($area->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/areas');
    }

    public function destroy($id)
    {
        $area = AreaDireito::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($area->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}