<?php

namespace App\Http\Controllers;

use App\Cargo;
use App\Http\Requests\CargoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class CargoController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        $cargos = Cargo::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_cargo_car')->get();   
        return view('configuracoes/cargos',['cargos' => $cargos]);
    }

    public function show($id)
    {
        $cargo = Cargo::findOrFail($id);     
        return response()->json($cargo);  
    }

    public function store(CargoRequest $request)
    {
        $cargo = new Cargo();
 
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $cargo->fill($request->all());

        if($cargo->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/cargos');

    }

    public function update(Request $request,$id)
    {
        $cargo = Cargo::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $cargo->fill($request->all());

        if($cargo->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/cargos');
    }

    public function destroy($id)
    {
        $cargo = Cargo::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($cargo->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}