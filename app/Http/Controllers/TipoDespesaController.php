<?php

namespace App\Http\Controllers;

use App\Area;
use App\TipoDespesa;
use App\Http\Requests\TipoDespesaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class TipoDespesaController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = 1;
    }

    public function index()
    {

        $tipos = TipoDespesa::where('cd_conta_con', $this->cdContaCon)->get();
        
        return view('configuracoes/tipos-de-despesa',['tipos' => $tipos]);
    }

    public function show($id)
    {
        
        $area = AreaDireito::findOrFail($id);     
        return response()->json($area);  
    }

    public function store(TipoDespesaRequest $request)
    {
        $tipo = new TipoDespesa();

        if(isset($request->fl_reembolso_tds)){
            $request->merge(['fl_reembolso_tds' => 'S']);
        }else{
            $request->merge(['fl_reembolso_tds' => 'N']);
        }
        
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados inseridos com sucesso');
        else
			Flash::error('Erro ao inserir dados');
        
        return redirect('configuracoes/tipos-de-despesa');

    }

    public function update(Request $request,$id)
    {
        $tipo = TipoDespesa::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);

        if(isset($request->fl_reembolso_tds)){
            $request->merge(['fl_reembolso_tds' => 'S']);
        }else{
            $request->merge(['fl_reembolso_tds' => 'N']);
        }
        
        $request->merge(['cd_conta_con' => $this->cdContaCon]);

        $tipo->fill($request->all());

        if($tipo->saveOrFail())
        	Flash::success('Dados atualizados com sucesso');
        else
			Flash::error('Erro ao atualizar dados');

        return redirect('configuracoes/tipos-de-despesa');
    }

    public function destroy($id)
    {
        $tipo = TipoDespesa::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($tipo->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}