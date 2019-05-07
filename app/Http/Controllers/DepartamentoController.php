<?php

namespace App\Http\Controllers;

use App\Departamento;
use App\Http\Requests\DepartamentoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class DepartamentoController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

        $departamentos = Departamento::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_departamento_dep')->get();   
        return view('configuracoes/departamentos',['departamentos' => $departamentos]);
    }

    public function show($id)
    {
        $departamento = Departamento::findOrFail($id);     
        return response()->json($departamento);  
    }

    public function store(DepartamentoRequest $request)
    {
        $departamento = new Departamento();
 
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $departamento->fill($request->all());

        if($departamento->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/departamentos');

    }

    public function update(Request $request,$id)
    {
        $departamento = Departamento::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $departamento->fill($request->all());

        if($departamento->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/departamentos');
    }

    public function destroy($id)
    {
        $departamento = Departamento::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($departamento->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}