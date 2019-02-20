<?php

namespace App\Http\Controllers;

use App\Vara;
use App\Http\Requests\VaraRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

class VaraController extends Controller
{

    private $cdContaCon;

    public function __construct()
    {
        $this->middleware('auth');
        $this->cdContaCon = 1;
    }

    public function index()
    {

        $varas = Vara::where('cd_conta_con', $this->cdContaCon)->get();   
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

    public function update(Request $request,$id)
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
        $vara = Vara::where('cd_conta_con',$this->cdContaCon)->findOrFail($id);
        
        if($vara->delete())
        	return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
        	return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
    }
}