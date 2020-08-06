<?php

namespace App\Http\Controllers;

use App\CategoriaDespesa;
use App\Http\Requests\CategoriaDespesaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class CategoriaDespesaController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {
        $categorias = CategoriaDespesa::where('cd_conta_con', $this->cdContaCon)->orderBy('nm_categoria_despesa_cad')->get();   
        return view('configuracoes/categorias-de-despesas',['categorias' => $categorias]);
    }

    public function show($id)
    {
        $vara = CategoriaDespesa::findOrFail($id);     
        return response()->json($vara);  
    }

    public function store(CategoriaDespesaRequest $request)
    {
        $vara = new CategoriaDespesa();
 
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/categorias-de-despesas');

    }

    public function update(Request $request,$id)
    {
        $vara = CategoriaDespesa::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $vara->fill($request->all());

        if($vara->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/categorias-de-despesas');
    }

    public function destroy($id)
    {
        $vara = CategoriaDespesa::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($vara->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}