<?php

namespace App\Http\Controllers;

use App\CategoriaCorrespondente;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriaCorrespondenteRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class CategoriaCorrespondenteController extends Controller
{

    private $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        $categorias = CategoriaCorrespondente::where('cd_conta_con', $this->conta)->orderBy('dc_categoria_correspondente_cac')->get();   
        return view('correspondente/categorias-correspondente',['categorias' => $categorias]);
    }

    public function show($id)
    {
        $vara = CategoriaCorrespondente::findOrFail($id);     
        return response()->json($vara);  
    }

    public function store(CategoriaCorrespondenteRequest $request)
    {
        $vara = new CategoriaCorrespondente();
 
        $request->merge(['cd_conta_con' => $this->conta]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('correspondente/categorias');

    }

    public function update(Request $request,$id)
    {
        $vara = CategoriaCorrespondente::where('cd_conta_con',$this->conta)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->conta]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('correspondente/categorias');
    }

    public function destroy($id)
    {
        $vara = CategoriaCorrespondente::where('cd_conta_con',$this->conta)->findOrFail($id);
        
        if($vara->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}