<?php

namespace App\Http\Controllers;

use App\Despesa;
use App\TipoDespesa;
use App\CategoriaDespesa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests\DespesaRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;

class DespesasController extends Controller
{

    private $conta;

    public function __construct()
    {
        $this->middleware('auth');
        $this->conta = \Session::get('SESSION_CD_CONTA');
    }

    public function index()
    {

    }

    public function getTipos($id)
    {
        $tipos = TipoDespesa::where('cd_conta_con',$this->conta)->where('cd_categoria_despesa_cad',$id)->orderBy('nm_tipo_despesa_tds','ASC')->get();     
        return response()->json($tipos); 
    }

     public function getCategorias($id)
    {
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->where('cd_categoria_despesa_cad',$id)->orderBy('nm_categoria_despesa_cad','ASC')->first();     
        return response()->json($categorias); 
    }

    public function show($id)
    {   
        $despesa = Despesa::findOrFail($id);   
        return view('despesas/detalhes', ['despesa' => $despesa ]);
    }

    public function novo()
    {   
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();
        $despesas = TipoDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();

        return view('despesas/novo', ['despesas' => $despesas, 'categorias' => $categorias ]); 
    }

    public function editar($id)
    {   
        $despesa = Despesa::findOrFail($id);  
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();
        $despesas = TipoDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();

        return view('despesas/editar', ['despesa' => $despesa, 'despesas' => $despesas, 'categorias' => $categorias ]); 
    }

    public function store(DespesaRequest $request)
    {
        try {

            $request->vl_valor_des = str_replace(",", ".", $request->vl_valor_des);
            $request->merge(['vl_valor_des' => $request->vl_valor_des]);
            $request->merge(['cd_conta_con' => $this->conta]);

            $despesa = new Despesa();
            $despesa->fill($request->all());

            if($despesa->save()){
                Flash::success('Despesa cadastrada com sucesso');
                return redirect('despesas/lancamentos');
            }else{
                Flash::error('Erro ao cadastrar despesa. Verifique os dados e tente novamente');
                return redirect()->back();
            }
            
        }catch (Exception $e) {
            


        }

    }

    public function update(DespesaRequest $request, $id)
    {

        $request->vl_valor_des = str_replace(",", ".", $request->vl_valor_des);
        $request->merge(['vl_valor_des' => $request->vl_valor_des]);
        $request->merge(['cd_conta_con' => $this->conta]);

        $despesa = Despesa::findOrFail($id);
        $despesa->fill($request->all());

        if($despesa->save()){
            Flash::success('Despesa atualizada com sucesso');
            return redirect('despesas/lancamentos');
        }else{
            Flash::error('Erro ao atualizar despesa. Verifique os dados e tente novamente');
            return redirect()->back();
        }

    }

    public function lancamentos()
    {   
        $categorias = CategoriaDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_categoria_despesa_cad','ASC')->get();
        $despesas = TipoDespesa::where('cd_conta_con',$this->conta)->orderBy('nm_tipo_despesa_tds','ASC')->get();
        $lancamentos = Despesa::where('cd_conta_con', $this->conta)->orderBy('dt_vencimento_des')->get();

        return view('despesas/lancamentos', ['lancamentos' => $lancamentos, 'despesas' => $despesas, 'categorias' => $categorias]); 
    }

    public function destroy($id)
    {   

        $despesa = Despesa::findOrFail($id);
        
        if($despesa->delete())
            return Response::json(array('message' => 'Registro excluído com sucesso'), 200);
        else
            return Response::json(array('message' => 'Erro ao excluir o registro'), 500);
        
    }
}